#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.env"
DUMP_PATH="${1:-$HOME/Downloads/wordlegroup.sql}"
TARGET_DB="${2:-wordlegroup_prod}"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Missing .env at $ENV_FILE" >&2
  exit 1
fi

if [[ ! -f "$DUMP_PATH" ]]; then
  echo "Dump file not found at $DUMP_PATH" >&2
  exit 1
fi

set -a
# shellcheck disable=SC1090
. "$ENV_FILE"
set +a

if [[ "${DB_CONNECTION:-}" != "mysql" ]]; then
  echo "DB_CONNECTION is not mysql in .env" >&2
  exit 1
fi

DB_HOST_VALUE="${DB_HOST:-127.0.0.1}"
DB_PORT_VALUE="${DB_PORT:-3306}"
DB_USER_VALUE="${DB_USERNAME:-root}"
DB_PASSWORD_VALUE="${DB_PASSWORD:-}"

MYSQL_ENV=("MYSQL_PWD=$DB_PASSWORD_VALUE")
MYSQL_ARGS=("--host=$DB_HOST_VALUE" "--port=$DB_PORT_VALUE" "--user=$DB_USER_VALUE")

"${MYSQL_ENV[@]}" mysql "${MYSQL_ARGS[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$TARGET_DB\`;"
"${MYSQL_ENV[@]}" mysql "${MYSQL_ARGS[@]}" "$TARGET_DB" < "$DUMP_PATH"

echo "Imported $DUMP_PATH into $TARGET_DB"
