#!/bin/bash
# Deploy GoTrips: bring the server to exactly what is on GitHub's main branch.
#
#   bash deploy.sh            deploy now (run by hand over SSH)
#   bash deploy.sh github     same, run by GitHub Actions over SSH on every push
#                             (via deploy-entry.sh; see .github/workflows/deploy.yml)
#   bash deploy.sh --check    change nothing; record whether the server still
#                             matches GitHub (daily from GitHub Actions; exit 2
#                             drift, 3 behind, 4 ahead; feeds the Super Admin warning)
#
# GitHub is the only source of truth. From 19 Aug to 21 Oct 2026 every
# deploy failed because files had been edited or uploaded on the server
# directly, while the webhook still reported success, so the live site drifted
# two months away from git without anyone noticing. This script therefore:
#   - refuses to deploy over files changed on the server, lists them, and
#     saves their changes to storage/logs/ so nothing is lost;
#   - only ever fast-forwards to origin/main (the server never makes commits);
#   - writes the result to storage/app/deploy-status.json, which the Super
#     Admin dashboard shows as a red warning when something is wrong.
# Never edit or upload code on the server. Commit, push to main, and let this run.

set -uo pipefail

# Bash reads a script as it runs, and this script updates itself (git merge
# replaces deploy.sh mid-run). Run from a private copy so that can't happen.
if [ -z "${DEPLOY_SH_COPY:-}" ]; then
    COPY=$(mktemp "${TMPDIR:-/tmp}/gotrips-deploy.XXXXXX") || exit 1
    cp "$0" "$COPY"
    DEPLOY_SH_COPY="$COPY" exec bash "$COPY" "$@"
fi
trap 'rm -f "$DEPLOY_SH_COPY"' EXIT

SITE_DIR="${DEPLOY_SITE_DIR:-$HOME/domains/gotrips.ai/public_html}"
cd "$SITE_DIR" || { echo "DEPLOY FAILED — cannot enter $SITE_DIR" >&2; exit 1; }

MODE="${1:-manual}"
LOGFILE="storage/logs/deploy.log"
STATUS="storage/app/deploy-status.json"
STAMP=$(date -u +%Y%m%d-%H%M%S)
mkdir -p storage/logs storage/app

say() { echo "$1"; echo "$1" >> "$LOGFILE"; }

# One deploy at a time: two pushes in quick succession must not interleave.
exec 9>storage/app/deploy.lock
if ! flock -n 9; then
    say "Another deploy is running; this one ($MODE) exits without changes."
    exit 0
fi

# Status for the Super Admin dashboard. $1 ok|failed|drift|behind, $2 message,
# $3 file list (newline separated). Hostinger disables shell_exec in PHP, so
# everything git knows is worked out here in bash and passed in.
write_status() {
    php -r '
        $files = array_values(array_filter(explode("\n", $argv[3])));
        file_put_contents($argv[4], json_encode([
            "state"      => $argv[1],
            "message"    => $argv[2],
            "files"      => array_slice($files, 0, 50),
            "file_count" => count($files),
            "commit"     => $argv[6],
            "github"     => $argv[7],
            "mode"       => $argv[5],
            "at"         => gmdate("c"),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    ' "$1" "$2" "${3:-}" "$STATUS" "$MODE" \
      "$(git rev-parse --short HEAD 2>/dev/null)" "$(git rev-parse --short origin/main 2>/dev/null)" \
      || echo "warning: could not write $STATUS" >&2
}

fail() {
    say ""
    say "!!! DEPLOY FAILED — $1"
    say "!!! Nothing was deployed. The site is still running the previous code."
    write_status failed "$1" "${2:-}"
    exit 1
}

# Files changed on the server: tracked files edited in place, and new code
# files that git does not know about. Uploaded media is ignored by .gitignore.
drift_files() {
    {
        git status --porcelain --untracked-files=no | cut -c4-
        git status --porcelain --untracked-files=all -- app bootstrap config database resources routes public/*.php | grep '^??' | cut -c4-
    } | sort -u
}

say ""
say "===== DEPLOY ($MODE): $(date -u '+%Y-%m-%d %H:%M:%S') UTC ====="

BRANCH=$(git rev-parse --abbrev-ref HEAD)
[ "$BRANCH" = "main" ] || fail "on branch '$BRANCH', expected 'main'. Run: git checkout main"

if ! git fetch origin main --quiet >> "$LOGFILE" 2>&1; then
    fail "could not fetch from GitHub (network or SSH key problem)"
fi

DRIFT=$(drift_files)
if [ -n "$DRIFT" ]; then
    PATCH="storage/logs/server-edits-$STAMP.patch"
    git diff > "$PATCH" 2>/dev/null
    COUNT=$(printf '%s\n' "$DRIFT" | wc -l | tr -d ' ')
    say "These $COUNT file(s) were changed on the server instead of in git:"
    printf '%s\n' "$DRIFT" | sed 's/^/    /' | tee -a "$LOGFILE"
    say "Their edits are saved in $PATCH."
    say "Put the changes in git (commit + push from your computer), then on the server run:"
    say "    git checkout -- . && git clean -fd -- app bootstrap config database resources routes && bash deploy.sh"
    if [ "$MODE" = "--check" ]; then
        write_status drift "$COUNT file(s) on the live server differ from git. Deploys are blocked until they are committed." "$DRIFT"
        exit 2
    fi
    fail "$COUNT file(s) were edited on the server instead of in git" "$DRIFT"
fi

LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)

if [ "$MODE" = "--check" ]; then
    if [ "$LOCAL" = "$REMOTE" ]; then
        write_status ok "Live site matches GitHub main."
        exit 0
    elif git merge-base --is-ancestor "$LOCAL" "$REMOTE"; then
        write_status behind "GitHub main has commits that are not deployed yet. Run deploy.sh."
        say "Behind GitHub: $(git rev-parse --short HEAD) is live, origin/main is $(git rev-parse --short origin/main)."
        exit 3
    else
        write_status failed "The server has commits that are not on GitHub main. It must be reset to origin/main."
        say "The server has commits that are not on GitHub main."
        exit 4
    fi
fi

if [ "$LOCAL" = "$REMOTE" ]; then
    say "Already at $(git rev-parse --short HEAD); running maintenance steps anyway."
elif ! git merge-base --is-ancestor "$LOCAL" "$REMOTE"; then
    fail "the server has commits that are not on GitHub main. Reset it: git reset --hard origin/main"
fi

BEFORE=$(git rev-parse HEAD)
if ! git merge --ff-only origin/main >> "$LOGFILE" 2>&1; then
    fail "could not move to origin/main (see the log above)"
fi
say "Now at commit $(git rev-parse --short HEAD) (was $(git rev-parse --short "$BEFORE"))"

if ! git diff --quiet "$BEFORE" HEAD -- composer.json composer.lock; then
    say "composer.lock changed: installing dependencies…"
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader >> "$LOGFILE" 2>&1 \
        || fail "composer install failed — the app may not boot"
else
    say "Updating autoloader…"
    composer dump-autoload --no-interaction >> "$LOGFILE" 2>&1 \
        || fail "composer dump-autoload failed — the app may not boot"
fi

say "Clearing caches…"
for CMD in "route:clear" "config:clear" "cache:clear" "view:clear"; do
    php artisan $CMD >> "$LOGFILE" 2>&1 || fail "php artisan $CMD failed"
done

say "Running migrations…"
php artisan migrate --force >> "$LOGFILE" 2>&1 \
    || fail "migrations failed — the database may be half-migrated, check it before retrying"

write_status ok "Deployed $(git rev-parse --short HEAD)."
say "Deploy completed at $(date -u '+%Y-%m-%d %H:%M:%S') UTC — now at $(git rev-parse --short HEAD)"
