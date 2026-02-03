<?php

use App\Models\Comment;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Discussion Feed Tests
|--------------------------------------------------------------------------
*/

it('can view social feed on group page when member', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $this->actingAs($user)
        ->get("/group/{$group->id}")
        ->assertOk()
        ->assertSee('Discussion');
});

it('can view social feed on public group when not member', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->create([
        'admin_user_id' => $admin->id,
        'verified_at' => now(),
        'public' => true,
    ]);
    GroupMembership::factory()->create(['user_id' => $admin->id, 'group_id' => $group->id]);

    $this->get("/group/{$group->id}")
        ->assertOk()
        ->assertSee('Discussion');
});

it('can create a discussion post as a group member', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    Livewire::actingAs($user)
        ->test(\App\Http\Livewire\Group\DiscussionFeed::class, ['group' => $group])
        ->set('body', 'Did anyone else struggle with this one?')
        ->call('createPost');

    expect(Comment::where('body', 'Did anyone else struggle with this one?')->exists())->toBeTrue();
});

it('cannot create a discussion post when not a member', function () {
    $admin = User::factory()->create();
    $outsider = User::factory()->create();
    $group = Group::factory()->create([
        'admin_user_id' => $admin->id,
        'verified_at' => now(),
        'public' => true,
    ]);
    GroupMembership::factory()->create(['user_id' => $admin->id, 'group_id' => $group->id]);

    Livewire::actingAs($outsider)
        ->test(\App\Http\Livewire\Group\DiscussionFeed::class, ['group' => $group])
        ->set('body', 'This should fail')
        ->call('createPost');

    expect(Comment::where('body', 'This should fail')->exists())->toBeFalse();
});

it('can reply to a discussion post', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $post = Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'body' => 'Original content',
    ]);

    Livewire::actingAs($user)
        ->test(\App\Http\Livewire\Group\DiscussionFeed::class, ['group' => $group])
        ->call('startReplying', $post)
        ->set('replyBody', 'This is my reply')
        ->call('saveReply');

    expect(Comment::where('body', 'This is my reply')->where('parent_id', $post->id)->exists())->toBeTrue();
});

it('can delete own post', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $post = Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'body' => 'Will be deleted',
    ]);

    Livewire::actingAs($user)
        ->test(\App\Http\Livewire\Group\DiscussionFeed::class, ['group' => $group])
        ->call('deletePost', $post);

    expect(Comment::withTrashed()->find($post->id)->trashed())->toBeTrue();
});

it('marks discussions as viewed on mount for members', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    $membership = GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    expect($membership->fresh()->last_viewed_discussions_at)->toBeNull();

    Livewire::actingAs($user)
        ->test(\App\Http\Livewire\Group\DiscussionFeed::class, ['group' => $group]);

    expect($membership->fresh()->last_viewed_discussions_at)->not->toBeNull();
});

it('dispatches discussionPosted event after creating post', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    Livewire::actingAs($user)
        ->test(\App\Http\Livewire\Group\DiscussionFeed::class, ['group' => $group])
        ->set('body', 'Test message')
        ->call('createPost')
        ->assertDispatched('discussionPosted');
});

it('loads earlier messages when requested', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    // Create 20 posts (more than displayLimit of 15)
    for ($i = 0; $i < 20; $i++) {
        Comment::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'body' => "Message {$i}",
        ]);
    }

    $component = Livewire::actingAs($user)
        ->test(\App\Http\Livewire\Group\DiscussionFeed::class, ['group' => $group]);

    expect($component->get('showingEarlier'))->toBeFalse();

    $component->call('showEarlierMessages');

    expect($component->get('showingEarlier'))->toBeTrue();
});

it('counts unread discussions correctly', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    $membership = GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    // No posts, should be 0 unread
    expect($group->getUnreadDiscussionCountFor($user))->toBe(0);

    // Create a post
    Comment::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'body' => 'Test post',
    ]);

    // Should be 1 unread (never viewed)
    expect($group->getUnreadDiscussionCountFor($user))->toBe(1);

    // Mark as viewed
    $membership->markDiscussionsAsViewed();

    // Should be 0 unread now
    expect($group->getUnreadDiscussionCountFor($user))->toBe(0);
});
