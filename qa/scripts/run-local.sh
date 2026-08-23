#!/usr/bin/env bash
# Run the same QA pipeline on your Mac (uses your local `claude` login — no API
# key needed). Handy for checking a change before pushing, or for re-running
# the gate manually.
#
# Usage:
#   qa/scripts/run-local.sh [base_url]
#     base_url defaults to http://127.0.0.1:8000 (start `php artisan serve` first)
#
# Env (optional): FATHOM_API_KEY, FATHOM_KEYWORD, TEAMS_WEBHOOK_URL,
#                 QA_ADMIN_EMAIL, QA_ADMIN_PASSWORD, CLAUDE_QA_MODEL
set -euo pipefail
cd "$(dirname "$0")/../.."

BASE_URL="${1:-http://127.0.0.1:8000}"
MODEL="${CLAUDE_QA_MODEL:-claude-sonnet-5}"
mkdir -p qa/out

echo "==> Diff under review (working tree + last commit vs origin/main)"
RANGE="origin/main...HEAD"
git fetch origin main --quiet || true
git diff --stat "$RANGE" > qa/out/diff-stat.txt || true
git diff "$RANGE" -- . ':(exclude)*.png' ':(exclude)*.jpg' ':(exclude)*.zip' ':(exclude)vendor' ':(exclude)node_modules' ':(exclude)composer.lock' ':(exclude)package-lock.json' > qa/out/diff.patch || true
git log --oneline "$RANGE" > qa/out/commits.txt 2>/dev/null || git log --oneline -5 > qa/out/commits.txt
head -c 400000 qa/out/diff.patch > qa/out/diff.trimmed.patch

echo "==> PHPUnit"
vendor/bin/phpunit --no-coverage 2>&1 | tee qa/out/phpunit.txt || true

echo "==> Fathom meeting"
bash qa/scripts/fetch-fathom.sh || true

echo "==> Agent 1: requirements checklist"
if [ -s qa/out/meeting.md ]; then
  claude -p --model "$MODEL" --dangerously-skip-permissions \
    "$(cat qa/prompts/extract-requirements.md)" | tee qa/out/requirements.md
elif [ -s qa/requirements-override.md ]; then
  cp qa/requirements-override.md qa/out/requirements.md
else
  echo "No meeting or override found — general QA only." > qa/out/requirements.md
fi

echo "==> Agent 2: backend review"
claude -p --model "$MODEL" --dangerously-skip-permissions \
  "$(cat qa/prompts/backend-review.md)" | tee qa/out/backend-report.md

echo "==> Agent 3: frontend persona testing against $BASE_URL"
if ! curl -sf "$BASE_URL" > /dev/null; then
  echo "!! $BASE_URL is not responding. Start the app first: php artisan serve"
  exit 1
fi
{
  echo "BASE URL TO TEST: $BASE_URL"
  echo "APP BOOTED FROM MIGRATIONS IN CI: local (real local data)"
  [ -n "${QA_ADMIN_EMAIL:-}" ] && echo "ADMIN LOGIN AVAILABLE: $QA_ADMIN_EMAIL / ${QA_ADMIN_PASSWORD:-}"
  echo ""
  cat qa/prompts/frontend-personas.md
} > qa/out/frontend-prompt.txt
claude -p "$(cat qa/out/frontend-prompt.txt)" --model "$MODEL" --dangerously-skip-permissions \
  --mcp-config qa/mcp-playwright.json | tee qa/out/frontend-report.md

echo "==> Verdicts"
get_verdict() { grep -oE 'VERDICT: *(PASS|WARN|BLOCK)' "$1" 2>/dev/null | tail -1 | grep -oE 'PASS|WARN|BLOCK' || echo "UNKNOWN"; }
BACKEND=$(get_verdict qa/out/backend-report.md)
FRONTEND=$(get_verdict qa/out/frontend-report.md)
OVERALL=PASS
{ [ "$BACKEND" = "WARN" ] || [ "$FRONTEND" = "WARN" ]; } && OVERALL=WARN
{ [ "$BACKEND" = "BLOCK" ] || [ "$FRONTEND" = "BLOCK" ]; } && OVERALL=BLOCK
echo "Backend: $BACKEND | Frontend: $FRONTEND | Overall: $OVERALL"

if [ -n "${TEAMS_WEBHOOK_URL:-}" ]; then
  OVERALL="$OVERALL" BACKEND="$BACKEND" FRONTEND="$FRONTEND" TESTS="local" \
    RUN_URL="local-run" bash qa/scripts/post-teams.sh || true
fi

[ "$OVERALL" = "BLOCK" ] && exit 1 || exit 0
