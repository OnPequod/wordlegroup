# Wordle Group - Project Context

## Overview
Wordle Group is a Laravel application for tracking and sharing Wordle scores with friends and groups. Users can record their daily Wordle scores, join groups, and compare performance with others.

## Tech Stack
- **Framework:** Laravel 12.x
- **PHP:** 8.4
- **Frontend:** Livewire 4, Tailwind CSS, Alpine.js
- **Database:** MySQL 8.4
- **Cache/Queue:** Redis
- **Testing:** Pest 3.0, PHPUnit 11

## Docker Setup
All services run in Docker. Key containers:
- `wordle-group-php` - PHP-FPM application
- `wordle-group-nginx` - Web server (port 8033)
- `wordle-group-mysql` - MySQL database (port 3307 on host)
- `wordle-group-redis` - Redis cache/queue
- `wordle-group-mailpit` - Local email testing

## Environment Files

| File | Database | Purpose |
|------|----------|---------|
| `.env` | `wordlegroup_test` | Development/testing (seeded data) |
| `.env.prod_copy` | `wordlegroup_prod_copy` | Working with production data copy |

### Switching Environments
```bash
# Switch to prod copy
cp .env.prod_copy .env
docker exec wordle-group-php php artisan config:cache

# Switch back to test
# Edit .env: DB_DATABASE=wordlegroup_test
docker exec wordle-group-php php artisan config:cache
```

## Database Access
From host machine (IDE/Beekeeper):
- Host: `localhost` or `127.0.0.1`
- Port: `3307`
- User: `wordlegroup`
- Password: `wordlegroup-dev-secret`

## Testing
```bash
# Run all tests
docker exec wordle-group-php php artisan test

# Run specific test file
docker exec wordle-group-php php artisan test tests/Feature/SmokeTest.php
```

Test database is `wordlegroup_test` - tests use RefreshDatabase trait.

## Key Directories
- `app/Http/Livewire/` - Livewire components (36 total)
- `app/Models/` - Eloquent models
- `resources/views/livewire/` - Livewire component views
- `database/factories/` - Model factories for testing
- `database/seeders/` - Database seeders
- `tests/Feature/` - Feature/integration tests

## Key Models
- `User` - Users with auth tokens, login codes
- `Group` - Wordle groups with admin, verification
- `GroupMembership` - User-group relationships
- `GroupMembershipInvitation` - Pending invitations
- `Score` - Wordle scores with board data

## Authentication
Passwordless auth via:
1. Email link with auth token
2. Login code sent to email

Test user: `user@site.com` (in seeded test database)

## Common Commands
```bash
# Clear all caches
docker exec wordle-group-php php artisan config:cache
docker exec wordle-group-php php artisan view:clear

# Fresh migration with seed
docker exec wordle-group-php php artisan migrate:fresh --seed

# Reload prod copy database
docker exec -i wordle-group-mysql mysql -uroot -pwordlegroup-dev-secret wordlegroup_prod_copy < storage/app/dumps/wordlegroup.sql
```

## Livewire 4 Notes
- Uses `App\Http\Livewire` namespace (not default `App\Livewire`)
- `wire:model` is deferred by default (use `.live` for real-time)
- `wire:model.blur` replaces old `wire:model.lazy`
- Config cached for PHP-FPM compatibility

## Production Deployment

### Infrastructure
- **Server:** dockolith.pequod.dev (managed by ~/Projects/web-server-management)
- **Domain:** wordlegroup.pequod.dev (temporary), eventually wordlegroup.com
- **Deploy tool:** Kamal 2

### Ansible Setup
App-specific provisioning lives in `ansible/` directory:
- `ansible/inventory/production.yml` - Server connection details
- `ansible/playbooks/provision.yml` - Creates DB, user, storage dirs
- `ansible/playbooks/generate-secrets.yml` - Generates .kamal/secrets
- `ansible/vault/secrets.yml` - App secrets (encrypted)

### Deployment Workflow
```bash
# First time: provision app resources on server
cd ansible
make provision-production

# Generate Kamal secrets (pulls from both WSM and app vaults)
make secrets

# Deploy with Kamal (from project root)
cd ..
kamal deploy -d production
```

### Database Migration (from monolith.pequod.dev)
```bash
cd ansible

# Transfer dump only
make migrate-db-production

# Transfer and import
make migrate-db-production import=true
```

### Kamal Config Files
- `config/deploy.yml` - Staging (default)
- `config/deploy.production.yml` - Production overrides

### Secrets
Shared secrets (mysql_root_password, redis_password, kamal_registry_*) come from:
`~/Projects/web-server-management/vault/secrets.yml`

App secrets (app_key, db_password, aws_*, sentry_*) come from:
`ansible/vault/secrets.yml`
