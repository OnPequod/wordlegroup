<?php

namespace App\Models;

use App\Casts\WordleDailyStartTime;
use App\Concerns\SyncsDailyScoreToGroupMemberships;
use App\Concerns\SyncsDailyScoreToUser;
use App\Concerns\UpdatesGroupStats;
use App\Concerns\UpdatesLeaderboards;
use App\Concerns\WordleBoard;
use App\Concerns\WordleDate;
use App\Jobs\UpdateDailySummariesJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Score extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => WordleDailyStartTime::class,
    ];

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(function (Score $score) {
            // Always update daily summaries (includes WG-wide stats for all users)
            UpdateDailySummariesJob::dispatch($score->board_number);
        });
    }

    public function getBoardAttribute($value)
    {
        return Str::replace('⬛', '⬜', $value);
    }

    public function getBoardHtmlAttribute()
    {
        $board = $this->board;

        // Replace emoji with styled spans
        $board = Str::replace('🟩', '<span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-green-600"></span>', $board);
        $board = Str::replace('🟨', '<span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-yellow-500"></span>', $board);
        $board = Str::replace('⬜', '<span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-zinc-300"></span>', $board);
        $board = Str::replace("\r\n", '<br>', $board);
        $board = Str::replace("\n", '<br>', $board);

        return $board;
    }

    public function getBoardShareTextAttribute()
    {
        $board = $this->boardTitle . "\n\n" .
            $this->board;

        return $board;
    }

    public function getBoardShareTextWithUrlAttribute()
    {
        $board = $this->boardShareText;

        if ($this->user->public_profile || $this->public) {
            $board .= "\n\n" .
                route('score.share-page', $this);
        }

        return $board;
    }

    public function getBoardTitleAttribute()
    {
        return "Wordle {$this->board_number} " . $this->scoreDisplay . "/6" . ($this->hard_mode ? '*' : '');
    }

    public function getEndOfWordleDayAttribute()
    {
        return app(WordleDate::class)->getActiveBoardEndTime()->subMicrosecond();
    }

    public function getPublicAttribute()
    {
        return (bool)$this->shared_at;
    }

    public function getScoreDisplayAttribute()
    {
        return $this->score === 7 ? 'X' : $this->score;
    }

    public function membershipsScoreRecords()
    {
        return $this->belongsToMany(self::class, 'group_membership_score');
    }

    public function recordingUser()
    {
        return $this->belongsTo(User::class, 'recording_user_id');
    }

    public function scopeBoardNumber($query, $boardNumber)
    {
        return $query->where('board_number', $boardNumber);
    }

    public function scopeFor($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeOnWordleDay($query, $date)
    {
        return $query->boardNumber(app(WordleBoard::class)->getBoardNumberFromDate($date));
    }

    public function scopeRecordedBy($query, User $user)
    {
        return $query->where('recording_user_id', $user->id);
    }

    public function scopeRecordedByOwner($query, User $user)
    {
        return $query->whereRaw('user_id = recording_user_id');
    }

    public function scopeRecordingUserScoreFirst($query, $userId)
    {
        return $query->orderByRaw("FIELD(recording_user_id, '{$userId}') DESC");
    }

    public function scoreCannotBeSeenByUser(?User $viewingUser = null)
    {
        return !$this->scoreCanBeSeenByUser($viewingUser);
    }

    public function scoreCanBeSeenByUser(?User $viewingUser = null)
    {
        // If it's public
        if ($this->public) {
            return true;
        }

        // If anonymous and not public, no.
        if (!$viewingUser) {
            return false;
        }

        // If the viewing user is the user.
        if ($this->id === $viewingUser->id) {
            return true;
        }

        // If the viewing user is in a group with the user.
        if ($this->user->sharesGroupMembershipWithAnotherUser($viewingUser)) {
            return true;
        }

        return false;
    }

    public function scopeToday($query)
    {
        return $query->where('board_number', app(WordleDate::class)->activeBoardNumber);
    }

    public function boardCannotBeSeenByUser(?User $viewingUser = null)
    {
        return !$this->boardCanBeSeenByUser($viewingUser);
    }

    public function boardCanBeSeenByUser(?User $viewingUser = null)
    {
        // If score has been actively shared.
        if ($this->public) {
            return true;
        }

        // If today is past the board day.
        if (app(WordleDate::class)->todayIsAfterBoardNumberDay($this->board_number)) {
            return true;
        }

        // If user is not in any of your groups,

        // If user has submitted their score today.
        if ($viewingUser->recordedBoard($this->board_number) || $viewingUser->recordedScoreToday()) {
            return true;
        }

        return false;
    }

    public function syncToDailyScores()
    {
        app(SyncsDailyScoreToUser::class)->sync($this);
    }

    public function syncToGroupMemberships()
    {
        app(SyncsDailyScoreToGroupMemberships::class)->sync($this);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function hasBotScore()
    {
        return $this->bot_skill_score !== null || $this->bot_luck_score !== null;
    }

    public function getBotSkillDisplayAttribute()
    {
        return $this->bot_skill_score !== null ? $this->bot_skill_score . '/99' : null;
    }

    public function getBotLuckDisplayAttribute()
    {
        return $this->bot_luck_score !== null ? $this->bot_luck_score . '/99' : null;
    }

    public function scopeBetweenBoards($query, $startBoard, $endBoard)
    {
        return $query->where('board_number', '>=', $startBoard)
            ->where('board_number', '<=', $endBoard);
    }

    public function validForMembership(GroupMembership $membership)
    {
        // Must be from a board after joining group.
        // And by either recording user or graph admin.
        return $this->endOfWordleDay > $membership->created_at
            && (
                $this->recordedByUser() ||
                $this->recordedByAdmin($membership)
            );
    }

    public function recordedByUser()
    {
        return $this->user_id === $this->recording_user_id;
    }

    public function recordedByAdmin(GroupMembership $membership)
    {
        return $this->recording_user_id === $membership->group->admin->id;
    }
}
