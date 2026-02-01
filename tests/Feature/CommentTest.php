<?php

use App\Models\Comment;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Score;
use App\Models\User;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Comment Model Tests
|--------------------------------------------------------------------------
*/

it('can create a comment for a score', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $score = Score::withoutEvents(function () use ($user) {
        return Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
        ]);
    });

    $comment = Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'commentable_type' => Score::class,
        'commentable_id' => $score->id,
        'body' => 'Great game!',
    ]);

    expect($comment)->toBeInstanceOf(Comment::class);
    expect($comment->body)->toBe('Great game!');
    expect($comment->user_id)->toBe($user->id);
    expect($comment->isScoreComment())->toBeTrue();
    expect($comment->isDiscussionPost())->toBeFalse();
});

it('can create a discussion post', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $comment = Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'body' => 'Did anyone else find today hard?',
    ]);

    expect($comment->isDiscussionPost())->toBeTrue();
    expect($comment->isScoreComment())->toBeFalse();
    expect($comment->body)->toBe('Did anyone else find today hard?');
});

it('can create a reply to a comment', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $parent = Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'body' => 'Test body',
    ]);

    $reply = Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'parent_id' => $parent->id,
        'body' => 'This is a reply',
    ]);

    expect($reply->isReply())->toBeTrue();
    expect($reply->parent->id)->toBe($parent->id);
    expect($parent->replies->count())->toBe(1);
});

it('author can edit their own comment', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $comment = Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'body' => 'Test comment',
    ]);

    expect($comment->canBeEditedBy($user))->toBeTrue();
});

it('other users cannot edit comment', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $author->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $author->id, 'group_id' => $group->id]);

    $comment = Comment::create([
        'user_id' => $author->id,
        'group_id' => $group->id,
        'body' => 'Test comment',
    ]);

    expect($comment->canBeEditedBy($otherUser))->toBeFalse();
});

it('group admin can delete any comment', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $admin->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $admin->id, 'group_id' => $group->id]);
    GroupMembership::factory()->create(['user_id' => $member->id, 'group_id' => $group->id]);

    $comment = Comment::create([
        'user_id' => $member->id,
        'group_id' => $group->id,
        'body' => 'Test comment',
    ]);

    expect($comment->canBeDeletedBy($admin))->toBeTrue();
    expect($comment->canBeDeletedBy($member))->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Score Comments Livewire Component Tests
|--------------------------------------------------------------------------
*/

it('can view comments on a public score page', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $score = Score::withoutEvents(function () use ($user) {
        return Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'shared_at' => now(),
        ]);
    });

    $this->get("/score/{$score->id}")->assertOk();
});

it('can add a comment when logged in and sharing a group', function () {
    $scoreOwner = User::factory()->create();
    $commenter = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $scoreOwner->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $scoreOwner->id, 'group_id' => $group->id]);
    GroupMembership::factory()->create(['user_id' => $commenter->id, 'group_id' => $group->id]);

    $score = Score::withoutEvents(function () use ($scoreOwner) {
        return Score::factory()->create([
            'user_id' => $scoreOwner->id,
            'recording_user_id' => $scoreOwner->id,
            'shared_at' => now(),
        ]);
    });

    Livewire::actingAs($commenter)
        ->test(\App\Http\Livewire\Score\Comments::class, ['score' => $score, 'group' => $group])
        ->set('body', 'Nice score!')
        ->call('addComment');

    expect(Comment::where('body', 'Nice score!')->exists())->toBeTrue();
});
