<?php

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Score;
use App\Models\User;
use App\Services\ScoreExportService;

/*
|--------------------------------------------------------------------------
| Board Parsing Tests
|--------------------------------------------------------------------------
*/

it('parses a full board into 6 lines', function () {
    $service = new ScoreExportService();
    $board = "🟩🟩🟩🟩🟩\n🟨🟨🟨🟨🟨\n⬜⬜⬜⬜⬜\n🟩🟨⬜🟩🟨\n🟩🟩🟩🟩🟩\n🟩🟩🟩🟩🟩";

    $lines = $service->parseBoardLines($board);

    expect($lines)->toHaveCount(6);
    expect($lines[0])->toBe('🟩🟩🟩🟩🟩');
    expect($lines[5])->toBe('🟩🟩🟩🟩🟩');
});

it('handles null board by returning 6 empty lines', function () {
    $service = new ScoreExportService();

    $lines = $service->parseBoardLines(null);

    expect($lines)->toHaveCount(6);
    expect($lines)->each->toBe('');
});

it('handles empty board by returning 6 empty lines', function () {
    $service = new ScoreExportService();

    $lines = $service->parseBoardLines('');

    expect($lines)->toHaveCount(6);
    expect($lines)->each->toBe('');
});

it('pads partial boards to 6 lines', function () {
    $service = new ScoreExportService();
    $board = "🟩🟩🟩🟩🟩\n🟨🟨🟨🟨🟨\n🟩🟩🟩🟩🟩";

    $lines = $service->parseBoardLines($board);

    expect($lines)->toHaveCount(6);
    expect($lines[0])->toBe('🟩🟩🟩🟩🟩');
    expect($lines[2])->toBe('🟩🟩🟩🟩🟩');
    expect($lines[3])->toBe('');
    expect($lines[5])->toBe('');
});

it('handles windows line endings', function () {
    $service = new ScoreExportService();
    $board = "🟩🟩🟩🟩🟩\r\n🟨🟨🟨🟨🟨\r\n⬜⬜⬜⬜⬜";

    $lines = $service->parseBoardLines($board);

    expect($lines)->toHaveCount(6);
    expect($lines[0])->toBe('🟩🟩🟩🟩🟩');
    expect($lines[1])->toBe('🟨🟨🟨🟨🟨');
    expect($lines[2])->toBe('⬜⬜⬜⬜⬜');
});

/*
|--------------------------------------------------------------------------
| Member ID Mapping Tests
|--------------------------------------------------------------------------
*/

it('assigns member ids based on join order', function () {
    $admin = User::factory()->create();
    $member1 = User::factory()->create();
    $member2 = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $admin->id, 'verified_at' => now()]);

    // Create memberships in specific order with explicit created_at
    GroupMembership::factory()->create([
        'user_id' => $admin->id,
        'group_id' => $group->id,
        'created_at' => now()->subDays(3),
    ]);
    GroupMembership::factory()->create([
        'user_id' => $member1->id,
        'group_id' => $group->id,
        'created_at' => now()->subDays(2),
    ]);
    GroupMembership::factory()->create([
        'user_id' => $member2->id,
        'group_id' => $group->id,
        'created_at' => now()->subDays(1),
    ]);

    $service = new ScoreExportService();
    $memberIdMap = $service->getMemberIdMap($group);

    expect($memberIdMap[$admin->id])->toBe(1);
    expect($memberIdMap[$member1->id])->toBe(2);
    expect($memberIdMap[$member2->id])->toBe(3);
});

/*
|--------------------------------------------------------------------------
| Account Export Tests
|--------------------------------------------------------------------------
*/

it('requires authentication for account export', function () {
    $this->get(route('account.export.scores.csv'))
        ->assertRedirect(route('login'));
});

it('allows authenticated users to export their scores', function () {
    $user = User::factory()->create(['name' => 'Test User']);

    Score::withoutEvents(function () use ($user) {
        return Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'board_number' => 1000,
            'score' => 4,
            'hard_mode' => true,
            'board' => "🟨⬜⬜⬜⬜\n🟨🟨⬜⬜⬜\n🟩🟩🟩⬜⬜\n🟩🟩🟩🟩🟩",
        ]);
    });

    $response = $this->actingAs($user)->get(route('account.export.scores.csv'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    $response->assertHeader('Content-Disposition', 'attachment; filename="wordle-scores-' . now()->format('Y-m-d') . '.csv"');
});

it('returns empty csv with headers when user has no scores', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('account.export.scores.csv'));

    $response->assertOk();
    $content = $response->streamedContent();
    $lines = explode("\n", trim($content));

    expect($lines)->toHaveCount(1);
    expect($lines[0])->toContain('date,board_number,score');
});

/*
|--------------------------------------------------------------------------
| Group Export Tests
|--------------------------------------------------------------------------
*/

it('requires authentication for group export', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $admin->id, 'verified_at' => now()]);

    $this->get(route('group.export.scores.csv', $group))
        ->assertRedirect(route('login'));
});

it('denies non-admin users from exporting group scores', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $admin->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $admin->id, 'group_id' => $group->id]);
    GroupMembership::factory()->create(['user_id' => $member->id, 'group_id' => $group->id]);

    $this->actingAs($member)
        ->get(route('group.export.scores.csv', $group))
        ->assertForbidden();
});

it('allows admin to export group scores', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $admin->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $admin->id, 'group_id' => $group->id]);

    $response = $this->actingAs($admin)->get(route('group.export.scores.csv', $group));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
});

it('includes member_id and name in group export', function () {
    $admin = User::factory()->create(['name' => 'Admin User']);
    $group = Group::factory()->create(['admin_user_id' => $admin->id, 'verified_at' => now()]);
    $membership = GroupMembership::factory()->create(['user_id' => $admin->id, 'group_id' => $group->id]);

    $score = Score::withoutEvents(function () use ($admin) {
        return Score::factory()->create([
            'user_id' => $admin->id,
            'recording_user_id' => $admin->id,
            'board_number' => 1000,
            'score' => 3,
        ]);
    });

    // Attach the score to the group via pivot with all required fields
    $group->scores()->attach($score->id, [
        'user_id' => $admin->id,
        'group_membership_id' => $membership->id,
        'board_number' => $score->board_number,
    ]);

    $response = $this->actingAs($admin)->get(route('group.export.scores.csv', $group));

    $response->assertOk();
    $content = $response->streamedContent();

    expect($content)->toContain('member_id,name,date');
    expect($content)->toContain('Admin User');
});

/*
|--------------------------------------------------------------------------
| Score Display Tests
|--------------------------------------------------------------------------
*/

it('displays failed games as X in export', function () {
    $service = new ScoreExportService();

    $score = new Score([
        'score' => 7,
        'board_number' => 1000,
        'hard_mode' => false,
    ]);

    $row = $service->scoreToAccountRow($score);

    expect($row[2])->toBe('X');
});

it('displays normal scores as numbers in export', function () {
    $service = new ScoreExportService();

    $score = new Score([
        'score' => 4,
        'board_number' => 1000,
        'hard_mode' => true,
    ]);

    $row = $service->scoreToAccountRow($score);

    expect($row[2])->toBe(4);
    expect($row[3])->toBe('true');
});
