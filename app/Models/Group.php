<?php

namespace App\Models;

use App\Concerns\UpdatesLeaderboards;
use App\Concerns\WordleBoard;
use App\Concerns\WordleDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory, Prunable, SoftDeletes;

    protected $casts = [
        'leaderboard' => 'collection',
    ];

    protected $dates = ['verified_at'];

    protected $guarded = [];

    protected $hidden = [
        'token',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function getAdminUrlAttribute()
    {
        return route('group.home', $this);
    }

    public function getVerifyUrlAttribute()
    {
        return route('group.verify', $this) . '?token=' . $this->token;
    }

    public function isAdmin(User $user)
    {
        if (!$user) {
            return false;
        }

        return $this->admin_user_id === $user->id;
    }

    public function isMemberOf(?User $user = null)
    {
        if (!$user) {
            return false;

        }

        return $user->memberships->pluck('group_id')->contains($this->id);
    }

    public function pendingInvitations()
    {
        return $this->hasMany(GroupMembershipInvitation::class, 'group_id');
    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMinutes(config('settings.unverified_group_expires_minutes')))
                     ->whereNull('verified_at');
    }

    public function updateStats()
    {
        $stats = $this->getSummaryStats(); // forever

        $this->update([
            'member_count'       => $stats['member_count'],
            'scores_recorded'    => $stats['scores_recorded'],
            'score_mean'         => $stats['score_mean'],
            'score_median'       => $stats['score_median'],
            'score_mode'         => $stats['score_mode'],
            'score_distribution' => $stats['score_distribution'],
            'leaderboard'        => $this->getLeaderBoard() // forever board
        ]);

        $this->updateLeaderboards(now());
    }

    public function getSummaryStats($startDate = null, $endDate = null)
    {
        [$startBoard, $endBoard] = app(WordleBoard::class)->getStartAndEndBoardsFromDates($startDate, $endDate);

        // Use query builder to avoid loading all scores into memory
        $scoresQuery = $this->scores()
            ->where('board_number', '>=', $startBoard)
            ->where('board_number', '<=', $endBoard);

        $scoresCount = $scoresQuery->count();
        $scoreValues = $scoresQuery->pluck('score');

        return [
            'member_count'       => $this->memberships()->count(),
            'scores_recorded'    => $scoresCount,
            'score_mean'         => $this->getMeanScore($scoreValues),
            'score_median'       => $this->getMedianScore($scoreValues),
            'score_mode'         => $this->getModeScore($scoreValues),
            'score_distribution' => $this->getScoreDistribution($scoreValues),
        ];
    }

    public function updateLeaderboards(?Carbon $when = null)
    {
        if (!$when) {
            $when = now();
        }

        app(UpdatesLeaderboards::class)->update($this, $when);
    }

    public function leaderboards()
    {
        return $this->hasMany(Leaderboard::class);
    }

    public function activeLeaderboards()
    {
        return $this->leaderboards()->active();
    }

    public function memberships()
    {
        return $this->hasMany(GroupMembership::class, 'group_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function discussionPosts()
    {
        return $this->comments()->discussionPosts();
    }

    public function getUnreadDiscussionCountFor(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        $membership = $this->memberships()->where('user_id', $user->id)->first();

        if (!$membership) {
            return 0;
        }

        $query = $this->discussionPosts();

        if ($membership->last_viewed_discussions_at) {
            $query->where('created_at', '>', $membership->last_viewed_discussions_at);
        }

        return $query->count();
    }

    public function scores()
    {
        return $this->belongsToMany(Score::class, 'group_membership_score');
    }

    public function getMeanScore($scores = null)
    {
        $scores = $scores ?? $this->scores;

        return $scores->isNotEmpty() ? (float)round($scores->average('score'), 2) : null;
    }

    public function getMedianScore($scores = null)
    {
        $scores = $scores ?? $this->scores;

        return $scores->isNotEmpty() ? (float)round($scores->median('score'), 1) : null;
    }

    public function getModeScore($scores = null)
    {
        $scores = $scores ?? $this->scores;

        return $scores->isNotEmpty() ? (int)collect($scores->mode('score'))->min() : null;
    }

    public function getScoreDistribution($scores = null)
    {
        $scores = $scores ?? $this->scores;

        return collect([1, 2, 3, 4, 5, 6, 7])
            ->mapWithKeys(function ($number) use ($scores) {
                return [
                    ($number === 7 ? 'X' : $number) => $scores->where('score', $number)->count(),
                ];
            });
    }

    /*
     * Gets leaderboard for all scores between start date and end date.
     */
    public function getLeaderBoard($startDate = null, $endDate = null)
    {
        [$startBoard, $endBoard] = app(WordleBoard::class)->getStartAndEndBoardsFromDates($startDate, $endDate);

        // Load memberships with users
        $this->loadMissing('memberships.user');

        // Get all scores in range with a single query, grouped by user
        $allScores = $this->scores()
            ->where('board_number', '>=', $startBoard)
            ->where('board_number', '<=', $endBoard)
            ->get(['user_id', 'score'])
            ->groupBy('user_id');

        $userScores = $this->memberships
            ->map(function ($membership) use ($allScores) {
                $userScoreValues = $allScores->get($membership->user_id, collect())->pluck('score');

                return [
                    'user_id' => $membership->user_id,
                    'name'    => $membership->user->name,
                    'stats'   => [
                        'median' => $userScoreValues->isNotEmpty() ? round($userScoreValues->average(), 1) : null,
                        'mean'   => $userScoreValues->isNotEmpty() ? round($userScoreValues->average(), 2) : null,
                        'mode'   => $userScoreValues->isNotEmpty() ? collect($userScoreValues->mode())->min() : null,
                        'count'  => $userScoreValues->count(),
                    ],
                ];
            })
            ->reject(fn($userScore) => $userScore['stats']['count'] === 0);

        $placeNumbers = $userScores->pluck('stats.mean')
                                   ->unique()
                                   ->sort()
                                   ->values()
                                   ->map(function ($score, $index) {
                                       return [
                                           'place' => $index + 1,
                                           'score' => $score,
                                       ];
                                   });

        $leaderboard = $userScores
            ->map(function ($userScore) use ($placeNumbers) {
                $place = $placeNumbers->firstWhere('score', $userScore['stats']['mean'])['place'];

                $userScore['place'] = $place;

                return $userScore;
            })
            ->sortBy('place')
            ->values();

        return $leaderboard;
    }

    public function verified()
    {
        return (bool)$this->verified_at;
    }

    public function verify()
    {
        $this->update(['verified_at' => now(), 'token' => null]);
    }

}
