<?php

namespace Database\Seeders;

use App\Concerns\WordleBoard;
use App\Concerns\WordleDate;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\GroupMembershipInvitation;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Wordle-themed group names for realistic seeding.
     */
    protected array $groupNames = [
        'Family Wordle Warriors',
        'Office Word Nerds',
        'The Daily Wordlers',
        'Puzzle Pals',
        'Word Up Crew',
        'The Guess Gang',
        'Five Letter Friends',
        'Wordle Wizards',
        'The Green Square Club',
        'Brain Train Gang',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $groupCount = (int) env('SEED_GROUP_COUNT', 5);
        $minScores = (int) env('SEED_MIN_SCORES', 5);
        $maxScores = (int) env('SEED_MAX_SCORES', 14);

        // Use recent board numbers (last 2 weeks)
        $currentBoard = app(WordleBoard::class)->activeBoardNumber;
        $startBoard = $currentBoard - $maxScores - 1;

        // Create groups with members
        collect(range(1, $groupCount))->each(
            fn($i) => $this->createGroup($i, $startBoard, $minScores, $maxScores)
        );

        // Add users to additional groups (cross-membership)
        $this->addCrossGroupMemberships();

        // Create pending invitations for test user
        $this->createPendingInvitations();

        // Create one unverified user and group for testing verification flows
        $this->createUnverifiedEntities();

        $this->command->info('Seeding complete!');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Users', User::count()],
                ['Groups', Group::count()],
                ['Memberships', GroupMembership::count()],
                ['Scores', Score::count()],
                ['Pending Invitations', GroupMembershipInvitation::count()],
            ]
        );
    }

    /**
     * Create a group with members and scores.
     */
    protected function createGroup(int $iteration, int $startBoard, int $minScores, int $maxScores): void
    {
        $startOfWordleDay = app(WordleDate::class)->getDateFromBoardNumber($startBoard);
        $groupCreatedAt = $startOfWordleDay->copy()->subHour();

        // First group gets the test user as admin
        if ($iteration === 1) {
            $admin = User::factory()->create([
                'email' => 'user@site.com',
                'name' => 'Test User',
            ]);
        } else {
            $admin = User::factory()->create();
        }

        // Create verified group with realistic name
        $groupName = $this->groupNames[$iteration - 1] ?? "Wordle Group {$iteration}";
        $group = Group::factory()
            ->for($admin, 'admin')
            ->create([
                'name' => $groupName,
                'verified_at' => $groupCreatedAt,
                'created_at' => $groupCreatedAt,
            ]);

        // Add admin as member
        GroupMembership::factory()->create([
            'user_id' => $admin->id,
            'group_id' => $group->id,
            'created_at' => $groupCreatedAt,
        ]);

        // Create 3-5 additional members
        $memberCount = random_int(3, 5);
        GroupMembership::factory()
            ->count($memberCount)
            ->create([
                'group_id' => $group->id,
                'created_at' => $groupCreatedAt,
            ]);

        // Create scores for all members
        $group->memberships()->with('user')->get()->each(function ($membership) use ($startBoard, $minScores, $maxScores) {
            $this->createScoresForUser($membership->user, $startBoard, $minScores, $maxScores);
        });

        // Add 1-2 pending invitations per group
        $this->createGroupInvitations($group, $admin, random_int(1, 2));
    }

    /**
     * Create scores for a user with variety.
     */
    protected function createScoresForUser(User $user, int $startBoard, int $minScores, int $maxScores): void
    {
        $scoreCount = random_int($minScores, $maxScores);

        Score::withoutEvents(function () use ($user, $startBoard, $scoreCount) {
            Score::factory()
                ->count($scoreCount)
                ->state(new Sequence(function ($sequence) use ($user, $startBoard) {
                    $boardNumber = $startBoard + $sequence->index;
                    $date = app(WordleDate::class)->getDateFromBoardNumber($boardNumber);
                    $scoreTime = app(WordleDate::class)->get($date)
                        ->addHours(random_int(0, 23))
                        ->addMinutes(random_int(0, 59));

                    // 10% chance of bricked score
                    $score = random_int(1, 10) === 1 ? 7 : random_int(2, 6);

                    // 15% chance of shared score
                    $sharedAt = random_int(1, 100) <= 15 ? $scoreTime : null;

                    // 20% chance of hard mode
                    $hardMode = random_int(1, 5) === 1;

                    return [
                        'user_id' => $user->id,
                        'recording_user_id' => $user->id,
                        'board_number' => $boardNumber,
                        'date' => $date,
                        'score' => $score,
                        'hard_mode' => $hardMode,
                        'shared_at' => $sharedAt,
                        'created_at' => $scoreTime,
                        'updated_at' => $scoreTime,
                    ];
                }))
                ->create();
        });
    }

    /**
     * Add users to additional groups for cross-membership.
     */
    protected function addCrossGroupMemberships(): void
    {
        $groups = Group::all();
        if ($groups->count() < 2) {
            return;
        }

        User::all()->each(function ($user) use ($groups) {
            $currentGroupIds = $user->memberships()->pluck('group_id');

            $groups->whereNotIn('id', $currentGroupIds)
                ->random(min(2, $groups->count() - $currentGroupIds->count()))
                ->each(function ($group) use ($user) {
                    GroupMembership::firstOrCreate([
                        'user_id' => $user->id,
                        'group_id' => $group->id,
                    ]);
                });
        });
    }

    /**
     * Create pending invitations for a group.
     */
    protected function createGroupInvitations(Group $group, User $invitingUser, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            GroupMembershipInvitation::factory()->create([
                'group_id' => $group->id,
                'inviting_user_id' => $invitingUser->id,
            ]);
        }
    }

    /**
     * Create pending invitations for the test user to accept.
     */
    protected function createPendingInvitations(): void
    {
        $testUser = User::where('email', 'user@site.com')->first();
        if (!$testUser) {
            return;
        }

        // Get groups the test user is NOT a member of
        $memberGroupIds = $testUser->memberships()->pluck('group_id');
        $otherGroups = Group::whereNotIn('id', $memberGroupIds)->take(2)->get();

        foreach ($otherGroups as $group) {
            GroupMembershipInvitation::factory()->create([
                'group_id' => $group->id,
                'inviting_user_id' => $group->admin_user_id,
                'email' => $testUser->email,
                'name' => $testUser->name,
            ]);
        }
    }

    /**
     * Create unverified user and group for testing verification flows.
     */
    protected function createUnverifiedEntities(): void
    {
        // Unverified user
        $unverifiedUser = User::factory()->unverified()->create([
            'email' => 'unverified@site.com',
            'name' => 'Unverified User',
        ]);

        // Unverified group
        $group = Group::factory()->create([
            'name' => 'Unverified Test Group',
            'admin_user_id' => $unverifiedUser->id,
            'verified_at' => null,
        ]);

        GroupMembership::factory()->create([
            'user_id' => $unverifiedUser->id,
            'group_id' => $group->id,
        ]);
    }
}
