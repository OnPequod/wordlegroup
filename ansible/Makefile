.PHONY: help provision-production provision-staging secrets migrate-db-production migrate-db-staging \
        sync-db ping-production ping-staging ping-monolith vault-edit vault-view

help:
	@echo "Wordle Group - Ansible Deployment"
	@echo ""
	@echo "Provision (create DB, user, storage dirs):"
	@echo "  make provision-production    Provision on dockolith.pequod.dev"
	@echo "  make provision-staging       Provision on staging.hatchery.lan"
	@echo ""
	@echo "Secrets:"
	@echo "  make secrets                 Generate .kamal/secrets for Kamal deploy"
	@echo "  make vault-edit              Edit app secrets"
	@echo "  make vault-view              View app secrets"
	@echo ""
	@echo "Database:"
	@echo "  make sync-db                 Sync prod DB to local (wordlegroup_prod_copy)"
	@echo "  make migrate-db-production   Migrate DB to new production server"
	@echo "  make migrate-db-production import=true"
	@echo "                               Migrate and import into new production"
	@echo ""
	@echo "Connectivity:"
	@echo "  make ping-production         Test production connectivity"
	@echo "  make ping-staging            Test staging connectivity"
	@echo "  make ping-monolith           Test monolith connectivity"
	@echo ""
	@echo "Deploy (run from project root):"
	@echo "  kamal deploy -d production   Deploy to production"
	@echo "  kamal deploy                 Deploy to staging"

# Provision
provision-production:
	ansible-playbook playbooks/provision.yml -i inventory/production.yml

provision-staging:
	ansible-playbook playbooks/provision.yml -i inventory/staging.yml

# Secrets
secrets:
	ansible-playbook playbooks/generate-secrets.yml

vault-edit:
	ansible-vault edit vault/secrets.yml

vault-view:
	ansible-vault view vault/secrets.yml

# Database
sync-db:
	ansible-playbook playbooks/sync-db-local.yml -i inventory/sources.yml

migrate-db-production:
	ansible-playbook playbooks/migrate-database.yml \
		-i inventory/sources.yml \
		-i inventory/production.yml \
		-e target=production \
		$(if $(import),-e import=$(import),)

migrate-db-staging:
	ansible-playbook playbooks/migrate-database.yml \
		-i inventory/sources.yml \
		-i inventory/staging.yml \
		-e target=staging \
		$(if $(import),-e import=$(import),)

# Connectivity
ping-production:
	ansible all -i inventory/production.yml -m ping

ping-staging:
	ansible all -i inventory/staging.yml -m ping

ping-monolith:
	ansible monolith -i inventory/sources.yml -m ping
