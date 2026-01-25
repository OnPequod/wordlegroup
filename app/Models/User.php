<?php

namespace App\Models;

use App\Concerns\GetsUsersInSharedGroupsWithAuthenticatedUser;
use App\Concerns\Tokens;
use App\Concerns\WordleDate;
use App\Mail\NudgeUser;
use App\Mail\UserVerification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Prunable, SoftDeletes;


    protected $fillable = [
        'name',
        'email',
        'password',
        'public_profile',
        'allow_digest_emails',
        'allow_reminder_emails',
        'last_reminded_at',
        'email_verified_at',
        'auth_token',
        'auth_token_generated_at',
        'daily_scores_recorded',
        'login_code',
        'login_code_generated_at',
        'daily_score_mean',
        'daily_score_median',
        'daily_score_mode',
        'score_distribution',
        'dismissed_email_notification',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'auth_token',
        'auth_token_generated_at',
        'login_code',
        'login_code_generated_at',
    ];

    protected $casts = [
        'score_distribution' => 'collection',
        'last_reminded_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'auth_token_generated_at' => 'datetime',
        'login_code_generated_at' => 'datetime',
    ];

    public function canBeNudged()
    {
        return $this->allow_reminder_emails &&
            (
                !$this->last_reminded_at ||
                now()->subHours(config('settings.hours_between_reminder_emails')) > $this->last_reminded_at
            );
    }

    public function dailyScoresForBoard($boardNumber)
    {
        return $this->dailyScores()
                    ->wherePivot('board_number', $boardNumber);
    }

    public function dailyScores()
    {
        return $this->belongsToMany(Score::class, 'user_score');
    }

    public function dismissEmailNotification()
    {
        $this->update(['dismissed_email_notification' => true]);
    }

    public function generateNewLoginCode()
    {
        $this->updateQuietly([
            'login_code'              => app(Tokens::class)->generateDigits(6),
            'login_code_generated_at' => now(),
        ]);
    }

    public function getAccountUrlAttribute()
    {
        return route('account', $this);
    }

    public function getAverageScoreAttribute()
    {
        return $this->scores()
                    ->orderByRaw("FIELD(recording_user_id, {$this->id}) DESC")
                    ->orderByDesc('board_number')
                    ->groupBy('board_number')
                    ->get();
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function getLoginUrlAttribute()
    {
        return route('login') . '?id=' . $this->id . '&token=' . $this->auth_token;
    }

    public function getPrivateProfileAttribute()
    {
        return !$this->public_profile;
    }

    public function getVerifyUrlAttribute()
    {
        return route('account.verify', $this) . '?token=' . $this->auth_token;
    }

    public function memberships()
    {
        return $this->hasMany(GroupMembership::class);
    }

    public function nudgeUser(User $nudgedBy)
    {
        $this->update([
            'last_reminded_at' => now(),
        ]);

        Mail::to($this->email)
            ->send(new NudgeUser($this, $nudgedBy));
    }

    public function pendingGroupInvitations()
    {
        return $this->hasMany(GroupMembershipInvitation::class, 'email', 'email');
    }

    public function profileCannotBeSeenBy(User $viewingUser = null)
    {
        return !$this->profileCanBeSeenBy($viewingUser);
    }

    public function scoresToday()
    {
        return $this->scores()->today();
    }

    public function recordedScoreToday()
    {
        return $this->scoresToday->count() > 0;
    }

    public function recordedBoard($boardNumber)
    {
        $key = "user:{$this->id}:recorded-board:{$boardNumber}";


        return Cache::remember(
            $key,
            10,
            function() use($boardNumber) {
                return $this->scores()
                            ->where('board_number', $boardNumber)
                            ->count() > 0;
            }
        );
    }

    public function profileCanBeSeenBy(User $viewingUser = null)
    {
        // If it's public
        if ($this->public_profile) {
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
        if ($this->sharesGroupMembershipWithAnotherUser($viewingUser)) {
            return true;
        }

        return false;
    }

    public function sharesGroupMembershipWithAnotherUser(User $otherUser)
    {
        // Get all users that the authorized user shares a group with, and see if the other user's
        // ID is in that group.
        return app(GetsUsersInSharedGroupsWithAuthenticatedUser::class)->users->pluck('id')->contains($otherUser->id);
    }

    public function getSharedGroupsWithAnotherUser(User $otherUser)
    {
        $myGroups = self::getGroupsOfUser($this);
        $otherUsersGroups = self::getGroupsOfUser($otherUser);

        return $myGroups->intersect($otherUsersGroups);
    }

    public static function getGroupsOfUser(User $user)
    {
        $user->load('memberships');

        return $user->memberships->pluck('group_id');
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subMinutes(config('settings.unverified_user_expires_minutes')))
                     ->whereNull('email_verified_at');
    }

    public function recordedGroupMembershipScores()
    {
        return $this->belongsToMany(Score::class, 'group_membership_score');
    }

    public function resetAuthToken()
    {
        $this->update(['auth_token' => null, 'auth_token_generated_at' => null]);
    }

    public function resetLoginCode()
    {
        $this->update(['login_code' => null, 'login_code_generated_at' => null]);
    }

    public function scopeTokenNotExpired($query)
    {
        return $query->where('auth_token_generated_at', '>', now()->subDay());
    }

    public function sendEmailVerificationNotification()
    {
        $this->generateNewAuthToken();

        Mail::to($this->email)
            ->send(new UserVerification($this));
    }

    public function generateNewAuthToken()
    {
        $token = app(Tokens::class)->generate();

        $this->updateQuietly([
            'auth_token'              => $token,
            'auth_token_generated_at' => now(),
        ]);
    }

    public function updateStats()
    {

        $this->updateQuietly([
            'daily_scores_recorded' => $this->dailyScores()->count(),
            'daily_score_mean'      => $this->getMeanDailyScore(),
            'daily_score_median'    => $this->getMedianDailyScore(),
            'daily_score_mode'      => $this->getModeDailyScore(),
            'score_distribution'    => $this->getScoreDistribution(),
        ]);
    }

    public function getMeanDailyScore()
    {
        return $this->dailyScores->isNotEmpty() ? (float)round($this->dailyScores()->average('score'), 2) : null;
    }

    public function getMedianDailyScore()
    {
        return $this->dailyScores->isNotEmpty() ? (float)round($this->dailyScores->median('score'), 1) : null;
    }

    public function getModeDailyScore()
    {
        return $this->dailyScores->isNotEmpty() ? collect($this->dailyScores->mode('score'))->min() : null;
    }

    public function getScoreDistribution()
    {
        return collect([1, 2, 3, 4, 5, 6, 7])
            ->mapWithKeys(function ($number) {
                return [
                    ($number === 7 ? 'X' : $number) => $this->dailyScores->where('score', $number)->count(),
                ];
            });
    }

    public function validateAuthToken($authToken)
    {
        return $this->tokenNotExpired() && $this->auth_token === $authToken;
    }

    public function tokenNotExpired()
    {
        return !$this->tokenExpired();
    }

    public function tokenExpired()
    {
        return $this->auth_token_generated_at < now()->subHours(config('settings.auth_token_valid_hours'));
    }

    public function validateLoginCode($providedCode)
    {
        return $this->validLoginCode($providedCode) && $this->loginCodeActive();
    }

    public function validLoginCode(string $providedCode)
    {
        return (string)$this->login_code === $providedCode;
    }

    public function loginCodeActive()
    {
        return !$this->loginCodeExpired();
    }

    public function loginCodeExpired()
    {
        if (!$this->login_code_generated_at) {
            return true;
        }

        return now() > $this->login_code_generated_at->addMinutes(config('settings.login_code_valid_minutes'));
    }

    public function verified()
    {
        return $this->email_verified_at !== null;
    }

    public function verifyEmail()
    {
        $this->update(['email_verified_at' => now()]);
    }
}
