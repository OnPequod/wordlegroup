# Wordle Group

A Laravel application for tracking and sharing Wordle scores with friends and groups.

**Production:** https://wordlegroup.com

## Tech Stack

- **Framework:** Laravel 12.x with Livewire 4
- **PHP:** 8.4
- **Frontend:** Tailwind CSS, Alpine.js
- **Database:** MySQL 8.4
- **Cache/Queue:** Redis with Horizon
- **Deployment:** Kamal 2

## Deployment

### Prerequisites

- Docker with buildx
- Kamal 2 (`gem install kamal`)
- Ansible (`brew install ansible` or `pip install ansible`)
- SSH access to target servers
- GitHub Container Registry access

### Server Infrastructure

Servers are provisioned using [web-server-management](https://github.com/onpequod/web-server-management) which sets up:
- Docker
- MySQL 8.4
- Redis
- kamal-proxy

### Initial Setup (New Server)

1. **Provision the server** (creates database, user, storage directories):

   ```bash
   cd ansible
   make provision-production
   ```

2. **Generate Kamal secrets** (creates `.kamal/secrets-common`):

   ```bash
   cd ansible
   make secrets
   ```

3. **Deploy**:

   ```bash
   kamal deploy -d production
   ```

### Regular Deployments

**Automatic (recommended):** Push to `main` branch triggers GitHub Actions deployment.

**Manual:**
```bash
kamal deploy -d production
```

### GitHub Actions Setup

The workflow at `.github/workflows/deploy.yml` auto-deploys on push to main.

**Required GitHub Secrets** (Settings → Secrets → Actions):

| Secret | Description |
|--------|-------------|
| `SSH_PRIVATE_KEY` | SSH key for accessing production server |
| `APP_KEY` | Laravel APP_KEY |
| `DB_USERNAME` | Database username |
| `DB_PASSWORD` | Database password |
| `REDIS_PASSWORD` | Redis password |
| `AWS_ACCESS_KEY_ID` | AWS credentials for SES |
| `AWS_SECRET_ACCESS_KEY` | AWS credentials for SES |
| `SENTRY_LARAVEL_DSN` | Sentry DSN |

`GITHUB_TOKEN` is automatically provided for GHCR access.

### Configuration Files

| File | Purpose |
|------|---------|
| `config/deploy.yml` | Staging deployment (default) |
| `config/deploy.production.yml` | Production deployment |
| `.kamal/secrets-common` | Secrets for Kamal (generated, not committed) |

### Ansible Commands

All commands run from the `ansible/` directory:

```bash
# View all available commands
make help

# Provision server (DB, user, storage)
make provision-production

# Generate Kamal secrets
make secrets

# Sync production database to local
make sync-db

# Migrate database to new server
make migrate-db-production import=true

# Edit vault secrets
make vault-edit
```

### Database Operations

**Sync production to local development:**

```bash
cd ansible
make sync-db
```

This dumps production, transfers it locally, and imports into `wordlegroup_prod_copy`.

**Migrate to new production server:**

```bash
cd ansible
make migrate-db-production import=true
```

### Secrets Management

Secrets are stored in Ansible Vault at `ansible/vault/secrets.yml`:

```bash
cd ansible
make vault-edit    # Edit secrets
make vault-view    # View secrets
```

Required secrets:
- `app_key` - Laravel APP_KEY
- `db_user` / `db_password` - Database credentials
- `redis_password` - Redis password
- `aws_access_key_id` / `aws_secret_access_key` - AWS credentials for SES
- `sentry_dsn` - Sentry error tracking

### Kamal Commands

```bash
# Deploy to production
kamal deploy -d production

# Deploy to staging
kamal deploy

# View logs
kamal app logs -d production

# Open Rails console equivalent
kamal app exec -d production "php artisan tinker"

# Run migrations
kamal app exec -d production "php artisan migrate"

# Rollback to previous version
kamal rollback -d production
```

## Local Development

See [CLAUDE.md](CLAUDE.md) for local development setup with Docker Compose.

Quick start:

```bash
docker compose up -d
```

The app runs at http://localhost:8033

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Cloudflare                           │
│                  (DNS + Proxy)                          │
└─────────────────────┬───────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────┐
│              dockolith.pequod.dev                       │
│  ┌────────────────────────────────────────────────────┐ │
│  │              kamal-proxy                           │ │
│  │         (SSL termination)                          │ │
│  └────────────────────┬───────────────────────────────┘ │
│                       │                                 │
│  ┌────────────────────▼───────────────────────────────┐ │
│  │         wordle-group container                     │ │
│  │  ┌─────────┐  ┌─────────┐  ┌─────────────────────┐ │ │
│  │  │  nginx  │──│ PHP-FPM │  │  Horizon (worker)   │ │ │
│  │  └─────────┘  └─────────┘  └─────────────────────┘ │ │
│  └────────────────────────────────────────────────────┘ │
│                       │                                 │
│  ┌────────────────────▼───────────────────────────────┐ │
│  │     MySQL 8.4          │       Redis               │ │
│  │  (host.docker.internal)                            │ │
│  └────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

## Directory Structure

```
ansible/                    # Deployment automation
├── inventory/              # Server inventories
├── playbooks/              # Ansible playbooks
├── vault/                  # Encrypted secrets
└── Makefile                # Convenience commands

config/
├── deploy.yml              # Kamal staging config
└── deploy.production.yml   # Kamal production config

docker/
└── production/             # Production Docker configs
    ├── Dockerfile
    ├── nginx.conf
    ├── supervisord.conf
    └── ...
```
