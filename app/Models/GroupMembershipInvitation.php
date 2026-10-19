<?php

namespace App\Models;

use App\Concerns\Tokens;
use App\Mail\GroupInvitationReminder;
use App\Mail\GroupMembershipInvitation as GroupMembershipInvitationMail;
use App\Mail\UserVerification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class GroupMembershipInvitation extends Model
{
    use HasFactory, Prunable;

    protected $guarded = [];

    protected $hidden = ['token'];

    protected $casts = [
        'reminded_at' => 'datetime',
    ];

    public function setEmailAttribute(?string $email): void
    {
        $this->attributes['email'] = User::normalizeEmail($email);
    }

    public function invitingUser()
    {
        return $this->belongsTo(User::class, 'inviting_user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function generateNewAuthToken()
    {
        $this->update([
            'token' => app(Tokens::class)->generate(),
        ]);
    }

    public static function createInvitation(Group $group, $email, $name, User $invitingUser = null)
    {
        $invitation = self::create([
            'email'            => $email,
            'name'             => $name,
            'group_id'         => $group->id,
            'inviting_user_id' => $invitingUser->id ?? Auth::id(),
            'token'            => app(Tokens::class)->generate(),
        ]);

        $invitation->sendInvitationEmail();
    }

    public function sendInvitationEmail()
    {
        Mail::to($this->email)
            ->send(new GroupMembershipInvitationMail($this));
    }

    public function sendReminderEmail()
    {
        Mail::to($this->email)
            ->send(new GroupInvitationReminder($this));
    }

    public function getInvitationUrlAttribute()
    {
        return route('group.invitation', $this) . '?token=' . $this->token;
    }

    public function prunable()
    {
        return static::where('created_at', '<=',
            now()->subDays(config('settings.unverified_group_membership_expires_days')));
    }
}
