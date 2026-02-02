<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMembership extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['verified_at', 'token_generated_at', 'last_viewed_discussions_at', 'last_viewed_activity_at'];

    protected $hidden = ['token', 'token_generated_at'];

    protected $touches = ['group'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->group->admin();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function scores()
    {
        return $this->belongsToMany(Score::class);
    }

    public function scoresForBoard($boardNumber)
    {
        return $this->scores()
                    ->wherePivot('board_number', $boardNumber);
    }

    public function markDiscussionsAsViewed(): void
    {
        $this->update(['last_viewed_discussions_at' => now()]);
    }
}
