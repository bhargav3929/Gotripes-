#!/bin/bash
# Auto-deploy script for GoTrips
# Pulls latest from GitHub main branch and runs Laravel maintenance commands.
# Can be called manually (bash deploy.sh) or from cron (bash deploy.sh --cron).

cd ~/domains/gotrips.ai/public_html

LOGFILE="storage/logs/deploy.log"
MODE="${1:-manual}"

# For cron mode: only deploy if there are new commits
if [ "$MODE" = "--cron" ]; then
    git fetch origin main --quiet 2>/dev/null
    LOCAL=$(git rev-parse HEAD)
    REMOTE=$(git rev-parse origin/main)

    if [ "$LOCAL" = "$REMOTE" ]; then
        # Already up to date, exit silently
        exit 0
    fi

    echo "" >> "$LOGFILE"
    echo "===== AUTO-DEPLOY (cron): $(date) =====" >> "$LOGFILE"
    echo "Local:  $LOCAL" >> "$LOGFILE"
    echo "Remote: $REMOTE" >> "$LOGFILE"
else
    echo "" >> "$LOGFILE"
    echo "===== MANUAL DEPLOY: $(date) =====" >> "$LOGFILE"
fi

# Pull latest code
git pull origin main >> "$LOGFILE" 2>&1

# Update autoloader
composer dump-autoload --no-interaction >> "$LOGFILE" 2>&1

# Clear caches
php artisan route:clear >> "$LOGFILE" 2>&1
php artisan config:clear >> "$LOGFILE" 2>&1
php artisan cache:clear >> "$LOGFILE" 2>&1
php artisan view:clear >> "$LOGFILE" 2>&1

# Run migrations
php artisan migrate --force >> "$LOGFILE" 2>&1

echo "Deploy completed at $(date)" >> "$LOGFILE"
