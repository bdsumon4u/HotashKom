#!/usr/bin/env bash
set -euo pipefail

####################################
# CONFIG
####################################
KEY_NAME="HOTASH"
SSH_KEY="$HOME/.ssh/$KEY_NAME"

####################################
# LOAD SOURCE DB FROM .env
####################################
ENV_FILE=".env"
if [[ ! -f "$ENV_FILE" ]]; then
    echo "❌ .env not found in source project"
    exit 1
fi

for v in DB_USERNAME DB_DATABASE DB_PASSWORD; do
    export "$v"=$(grep "^$v=" "$ENV_FILE" | cut -d= -f2- | tr -d '"' | tr -d "'")
done

####################################
# ARGUMENT PARSING
####################################
while [[ $# -gt 0 ]]; do
    case "$1" in
        -s|--site) [[ -n "${2-}" && "${2-}" != -* ]] && { target_site="$2"; shift 2; } || shift ;;
        -d|--domain) [[ -n "${2-}" && "${2-}" != -* ]] && { target_domain="$2"; shift 2; } || shift ;;
        -h|--host) [[ -n "${2-}" && "${2-}" != -* ]] && { ssh_host="$2"; shift 2; } || shift ;;
        -u|--uname) [[ -n "${2-}" && "${2-}" != -* ]] && { target_username="$2"; shift 2; } || shift ;;
        -db|--dbname) [[ -n "${2-}" && "${2-}" != -* ]] && { target_db_dbase="$2"; shift 2; } || shift ;;
        -dbu|--dbuser) [[ -n "${2-}" && "${2-}" != -* ]] && { target_db_uname="$2"; shift 2; } || shift ;;
        -dbp|--dbpass) [[ -n "${2-}" && "${2-}" != -* ]] && { target_db_upass="$2"; shift 2; } || shift ;;
        -mu|--mailuser) [[ -n "${2-}" && "${2-}" != -* ]] && { target_mail_user="$2"; shift 2; } || shift ;;
        -mp|--mailpass) [[ -n "${2-}" && "${2-}" != -* ]] && { target_mail_pass="$2"; shift 2; } || shift ;;
        -r|--rootdir) [[ -n "${2-}" && "${2-}" != -* ]] && { target_root_dir="$2"; shift 2; } || shift ;;
        *) echo "❌ Unknown option: $1"; exit 1 ;;
    esac
done

####################################
# VALIDATION & PROMPTS
####################################
prompt_required() {
    local var_name="$1"
    local prompt_text="$2"
    local is_secret="${3:-false}"
    local value="${!var_name-}"

    while [[ -z "$value" ]]; do
        if [[ "$is_secret" == "true" ]]; then
            read -r -s -p "$prompt_text: " value
            echo
        else
            read -r -p "$prompt_text: " value
        fi
    done

    printf -v "$var_name" '%s' "$value"
}

prompt_required target_site "Target site name (--site)"
prompt_required target_domain "Target domain (--domain)"
prompt_required ssh_host "SSH host (--host)"
prompt_required target_username "Target SSH username (--uname)"
prompt_required target_db_dbase "Target database name (--dbname)"
prompt_required target_db_uname "Target database user (--dbuser)"
prompt_required target_db_upass "Target database password (--dbpass)" true
prompt_required target_mail_user "Target mail user (--mailuser)"
prompt_required target_mail_pass "Target mail password (--mailpass)" true
prompt_required target_root_dir "Target root directory (--rootdir)"

####################################
# SSH SETUP (PERSISTENT CONNECTION)
####################################
TARGET="$target_username@$ssh_host"
SSH_OPTS="-T -i $SSH_KEY \
-o ControlMaster=auto \
-o ControlPersist=10m \
-o ControlPath=~/.ssh/cm-%r@%h:%p \
-o Compression=yes"

ssh-keyscan -H "$ssh_host" >> ~/.ssh/known_hosts 2>/dev/null || true

# Transfer SSH Private Key to target
echo "🔑 Setting up SSH key on target..."
scp $SSH_OPTS "$SSH_KEY" "$TARGET:.ssh/"
ssh $SSH_OPTS "$TARGET" "chmod 600 .ssh/$KEY_NAME"

####################################
# CLEAR SOURCE CACHE BEFORE COPYING
####################################
echo "🧹 Clearing source cache..."
./php artisan optimize:clear 2>/dev/null || true

####################################
# STREAM FILES (NO INTERMEDIATE FILE)
####################################
echo "📦 Copying files..."

tar \
  --exclude=storage/framework/sessions \
  --exclude=storage/framework/views \
  --exclude=storage/framework/cache \
  --exclude=storage/framework/testing \
  --exclude=storage/logs \
  --exclude=storage/debugbar \
  --exclude=storage/app/pathao* \
  --exclude=storage/app/mpdf \
  --exclude=bootstrap/cache \
  -czf - . \
| ssh $SSH_OPTS "$TARGET" "
    mkdir -p '$target_root_dir'
    cd '$target_root_dir'
    tar -xzf -
    
    # Create Laravel directories that were excluded
    mkdir -p storage/framework/{sessions,views,cache,cache/data,testing}
    mkdir -p storage/logs
    mkdir -p storage/debugbar
    mkdir -p bootstrap/cache
    
    # Fix ownership and permissions
    chown -R \$(whoami):\$(whoami) storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
    find storage -type f -exec chmod 664 {} \\;
    find storage -type d -exec chmod 775 {} \\;
"

####################################
# STREAM DATABASE (PIPE DIRECTLY)
####################################
echo "🗄️  Copying database..."

mysqldump --single-transaction \
  -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" \
| ssh $SSH_OPTS "$TARGET" "
    /usr/bin/mysql -u '$target_db_uname' -p'$target_db_upass' '$target_db_dbase'
"

####################################
# HELPER: Escape for sed substitution
####################################
escape_sed() {
    printf '%s\n' "$1" | sed -e 's/[\/&]/\\&/g'
}

escape_sed_pipe() {
    printf '%s\n' "$1" | sed -e 's/[\/&|]/\\&/g'
}

####################################
# REMOTE DEPLOY (SINGLE SSH SESSION)
####################################
echo "🚀 Deploying on remote server..."

ssh $SSH_OPTS "$TARGET" <<EOF
set -e

cd "$target_root_dir"

if [[ ! -f .env ]]; then
    echo "❌ ERROR: .env not found"
    exit 1
fi

####################################
# HELPER FUNCTIONS
####################################
escape_sed() {
    printf '%s\n' "\$1" | sed -e 's/[\/&]/\\&/g'
}

escape_sed_pipe() {
    printf '%s\n' "\$1" | sed -e 's/[\/&|]/\\&/g'
}

####################################
# UPDATE .env FILE FIRST (CRITICAL)
####################################
sed -i "s/APP_NAME=.*/APP_NAME='$(escape_sed "$target_site")'/g" .env
sed -i "s|APP_URL=.*|APP_URL=https://www.$target_domain|g" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$(escape_sed "$target_db_dbase")/g" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$(escape_sed "$target_db_uname")/g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$(escape_sed_pipe "$target_db_upass")|g" .env
sed -i "s/MAIL_HOST=.*/MAIL_HOST=mail.$target_domain/g" .env
sed -i "s/MAIL_USERNAME=.*/MAIL_USERNAME=$(escape_sed "$target_mail_user")/g" .env
sed -i "s|MAIL_PASSWORD=.*|MAIL_PASSWORD=$(escape_sed_pipe "$target_mail_pass")|g" .env
sed -i "s/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=$(escape_sed "$target_mail_user")/g" .env

####################################
# REGENERATE AUTOLOADER (CRITICAL)
####################################
# Composer caches absolute paths, must regenerate for new location
if [[ -f composer.json ]]; then
    ./php "\$([ -f "./composer.phar" ] && echo "./composer.phar" || command -v composer || echo /opt/cpanel/composer/bin/composer)" dump-autoload -o 2>/dev/null || echo "⚠️  Could not regenerate autoloader"
fi

####################################
# RUN LARAVEL COMMANDS
####################################
# Remove old symlink/directory first so storage:link can create fresh one
rm -rf public/storage storage/app/pathao*

./php artisan key:generate --force
./php artisan migrate --force
./php artisan storage:link

# Run custom deployment script if it exists
if [[ -f ./server_deploy.sh ]]; then
    ./server_deploy.sh
fi

EOF

echo "✅ Deployment completed successfully"