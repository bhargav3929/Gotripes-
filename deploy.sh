#!/bin/bash
# Auto-deploy script for GoTrips
# Pulls latest from GitHub main branch and runs Laravel maintenance commands.
# Can be called manually (bash deploy.sh) or from cron (bash deploy.sh --cron).
#
# Every step is checked. On 24 Jul 2026 this script printed "Deploy completed"
# five times in a row while `git pull` was aborting every time — the server sat
# 105 commits behind for hours and nothing in the output said so. A deploy that
# did not deploy must now say so, on the terminal as well as in the log.

set -uo pipefail

SITE_DIR=~/domains/gotrips.ai/public_html

cd "$SITE_DIR" || {
    echo "DEPLOY FAILED — cannot enter $SITE_DIR" >&2
    exit 1
}

LOGFILE="storage/logs/deploy.log"
MODE="${1:-manual}"

# Report to the terminal AND the log. Previously everything went to the log
# only, so a person running this by hand saw nothing at all.
say() {
    echo "$1"
    echo "$1" >> "$LOGFILE"
}

fail() {
    say ""
    say "!!! DEPLOY FAILED — $1"
    say "!!! Nothing was deployed. The site is still running the previous code."
    say "!!! See $SITE_DIR/$LOGFILE for details."
    exit 1
}

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

# The branch is worth checking before anything else. `git pull origin main`
# happily merges into whatever branch happens to be checked out, which is how
# the server ended up serving code from a stale feature branch.
BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$BRANCH" != "main" ]; then
    fail "on branch '$BRANCH', expected 'main'. Run: git checkout main"
fi

# Pull latest code — the step that actually matters.
say "Pulling latest code…"
if ! git pull origin main >> "$LOGFILE" 2>&1; then
    fail "git pull did not succeed (see the error above in the log)"
fi

BEFORE=$(git rev-parse --short HEAD)
say "Now at commit $BEFORE"

# Update autoloader
say "Updating autoloader…"
if ! composer dump-autoload --no-interaction >> "$LOGFILE" 2>&1; then
    fail "composer dump-autoload failed — the app may not boot"
fi

# Clear caches. A stale compiled view will keep serving the old page even
# though the new code is on disk, so treat a failure here as fatal too.
say "Clearing caches…"
for CMD in "route:clear" "config:clear" "cache:clear" "view:clear"; do
    if ! php artisan $CMD >> "$LOGFILE" 2>&1; then
        fail "php artisan $CMD failed"
    fi
done

# Run migrations
say "Running migrations…"
if ! php artisan migrate --force >> "$LOGFILE" 2>&1; then
    fail "migrations failed — the database may be half-migrated, check it before retrying"
fi

say "Deploy completed at $(date) — now at $BEFORE"
