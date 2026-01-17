# Seeder Improvements Plan

## Current Issues

1. **Groups not verified** - All groups have `verified_at = null`, blocking most features
2. **No invitations** - Can't test invitation acceptance flows
3. **No shared scores** - All `shared_at = null`, can't test public score pages
4. **Hardcoded board 362** - Outdated, scores from ~2022
5. **No score variety** - Everyone has exactly 7 scores, no bricked scores
6. **Generic group names** - Company names like "Langosh, Schoen and Rowe"
7. **No unverified users** - Can't test verification flows in dev
8. **Not configurable** - Hardcoded counts

## Planned Improvements

### 1. Verify Groups by Default
- Set `verified_at = now()` on group creation
- Keeps groups functional out of the box

### 2. Add Realistic Group Names
Create array of Wordle-themed group names:
- "Family Wordle Warriors"
- "Office Word Nerds"
- "The Wordlers"
- "Daily Puzzlers"
- etc.

### 3. Use Dynamic Board Numbers
- Calculate from current date instead of hardcoded 362
- `app(WordleBoard::class)->activeBoardNumber - 14` for recent scores

### 4. Add Score Variety
- Vary scores per user (3-14 random range)
- Include some bricked scores (score = 7)
- Include some hard mode scores
- Add some shared/public scores (`shared_at = now()`)

### 5. Add Pending Invitations
- Create 2-3 pending invitations per group
- Allows testing invitation flows

### 6. Add Unverified User/Group
- One unverified user for testing verification flow
- One unverified group for testing group verification

### 7. Make Configurable
- `SEED_GROUP_COUNT` env variable (default: 5)
- `SEED_MIN_SCORES` / `SEED_MAX_SCORES` for variety

### 8. Improve Test User Setup
- Keep `user@site.com` as primary test user
- Make them admin of first group
- Give them pending invitations to accept
- Give them scores to view

## Implementation Order

1. [x] Update GroupFactory with better names
2. [x] Update DatabaseSeeder to verify groups
3. [x] Add dynamic board number calculation
4. [x] Add score variety (count, bricked, shared)
5. [x] Add pending invitations creation
6. [x] Add unverified user/group
7. [x] Add env-based configuration
8. [x] Test full seeder flow

**Completed: 2026-01-17**

## Test User Credentials
- Email: `user@site.com`
- Auth: Passwordless (email link or code)
