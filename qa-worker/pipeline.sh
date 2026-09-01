#!/usr/bin/env bash
# GoTrips QA audit pipeline — runs inside the Railway container.
# Clones the repo (read-only), reviews the pushed diff with a backend agent,
# waits for the Hostinger auto-deploy to land, then tests the LIVE site with a
# frontend persona agent in a real browser, and posts the CTO report to Teams.
#
# Never touches any database and never boots the app: code review is read-only,
# frontend testing happens against the already-deployed live site.
#
# Env in: REPO, GITHUB_TOKEN, BEFORE, AFTER, ACTOR, REPORTS_DIR,
#         CLAUDE_CODE_OAUTH_TOKEN, LIVE_URL, DEPLOY_WAIT_SECONDS,
#         CLAUDE_QA_MODEL, FATHOM_API_KEY, FATHOM_KEYWORD, TEAMS_WEBHOOK_URL,
#         PUBLIC_URL, REPORTS_TOKEN, QA_ADMIN_EMAIL, QA_ADMIN_PASSWORD
set -uo pipefail

START_TS=$(date +%s)
LIVE_URL="${LIVE_URL:-https://gotrips.ai}"
MODEL="${CLAUDE_QA_MODEL:-claude-sonnet-5}"
DEPLOY_WAIT="${DEPLOY_WAIT_SECONDS:-300}"
REPORTS_DIR="${REPORTS_DIR:-/tmp/qa-last-run}"
WORK=$(mktemp -d /tmp/qa-run.XXXXXX)
trap 'rm -rf "$WORK"' EXIT

echo "=== QA audit: push by ${ACTOR:-unknown} on $REPO (${BEFORE:0:7}..${AFTER:0:7}) ==="

# ---- 1. Clone (read-only) and compute the diff under review
git clone --quiet --depth 100 "https://x-access-token:${GITHUB_TOKEN}@github.com/${REPO}.git" "$WORK/repo" \
  || { echo "FATAL: clone failed"; exit 1; }
cd "$WORK/repo"
mkdir -p qa/out

if [ -n "${BEFORE:-}" ] && [ -n "${AFTER:-}" ] \
   && git cat-file -e "$BEFORE" 2>/dev/null && git cat-file -e "$AFTER" 2>/dev/null; then
  RANGE="$BEFORE..$AFTER"
else
  RANGE="HEAD~1..HEAD"
fi
echo "Reviewing range: $RANGE" | tee qa/out/diff-range.txt
git diff --stat "$RANGE" > qa/out/diff-stat.txt 2>/dev/null || true
git diff "$RANGE" -- . ':(exclude)*.png' ':(exclude)*.jpg' ':(exclude)*.jpeg' ':(exclude)*.zip' \
  ':(exclude)vendor' ':(exclude)node_modules' ':(exclude)composer.lock' ':(exclude)package-lock.json' \
  > qa/out/diff.patch 2>/dev/null || true
git log --oneline "$RANGE" > qa/out/commits.txt 2>/dev/null || git log --oneline -3 > qa/out/commits.txt
head -c 400000 qa/out/diff.patch > qa/out/diff.trimmed.patch
echo "PHPUnit was not run: the cloud auditor is database-free by design (tests here point at a real DB). Judge test impact from the code alone." > qa/out/phpunit.txt

# ---- 2. Latest client meeting from Fathom -> requirements checklist
bash qa/scripts/fetch-fathom.sh || true
if [ -s qa/out/meeting.md ]; then
  claude -p --model "$MODEL" --dangerously-skip-permissions \
    "$(cat qa/prompts/extract-requirements.md)" | tee qa/out/requirements.md
elif [ -s qa/requirements-override.md ]; then
  cp qa/requirements-override.md qa/out/requirements.md
  echo "Using manual requirements override file."
else
  echo "No meeting or override found — agents will do general QA only." | tee qa/out/requirements.md
fi

# ---- 3. Backend code review (read-only, against the diff)
claude -p --model "$MODEL" --dangerously-skip-permissions \
  "$(cat qa/prompts/backend-review.md)" | tee qa/out/backend-report.md

# ---- 4. Wait until the Hostinger auto-deploy has had time to land
ELAPSED=$(( $(date +%s) - START_TS ))
REMAIN=$(( DEPLOY_WAIT - ELAPSED ))
if [ "$REMAIN" -gt 0 ]; then
  echo "Waiting ${REMAIN}s more for the live deploy to land..."
  sleep "$REMAIN"
fi
if ! curl -sf --max-time 30 -o /dev/null "$LIVE_URL"; then
  echo "WARNING: $LIVE_URL not reachable — frontend agent will report this."
fi

# ---- 5. Frontend persona testing against the LIVE site
{
  echo "BASE URL TO TEST: $LIVE_URL"
  echo ""
  echo "!!! THIS IS THE LIVE PRODUCTION WEBSITE WITH REAL CUSTOMERS AND REAL CLIENT DATA. Hard safety rules:"
  echo "- NEVER complete a payment or reach a step that charges money."
  echo "- NEVER submit a form whose successful submission creates a booking/enquiry record or sends an email/WhatsApp to anyone. You MAY type into forms and submit INVALID data to test validation (a rejected submission creates nothing). Stop before valid final submission."
  echo "- In any admin/manager area: navigate and read only. NEVER click save, delete, approve, or upload."
  echo "- If unsure whether an action has side effects, don't do it — note it as NOT-TESTABLE instead."
  echo ""
  echo "APP BOOTED FROM MIGRATIONS IN CI: no — this is production with real data, so empty-looking pages ARE meaningful findings here (INFRA label does not apply)."
  if [ -n "${QA_ADMIN_EMAIL:-}" ]; then echo "ADMIN LOGIN AVAILABLE (test account): $QA_ADMIN_EMAIL / ${QA_ADMIN_PASSWORD:-}"; fi
  echo ""
  cat qa/prompts/frontend-personas.md
} > qa/out/frontend-prompt.txt
# NOTE: the prompt must come BEFORE --mcp-config — that flag is variadic and
# would otherwise swallow the prompt text as a (too-long) config filename.
claude -p "$(cat qa/out/frontend-prompt.txt)" --model "$MODEL" --dangerously-skip-permissions \
  --disallowedTools "ScheduleWakeup,CronCreate,CronDelete,CronList,Monitor" \
  --mcp-config /app/mcp-playwright.json | tee qa/out/frontend-report.md

# ---- 6. Verdicts + Teams report
get_verdict() { grep -oE 'VERDICT: *(PASS|WARN|BLOCK)' "$1" 2>/dev/null | tail -1 | grep -oE 'PASS|WARN|BLOCK' || echo "UNKNOWN"; }
BACKEND=$(get_verdict qa/out/backend-report.md)
FRONTEND=$(get_verdict qa/out/frontend-report.md)
OVERALL=PASS
{ [ "$BACKEND" = "WARN" ] || [ "$FRONTEND" = "WARN" ]; } && OVERALL=WARN
{ [ "$BACKEND" = "BLOCK" ] || [ "$FRONTEND" = "BLOCK" ]; } && OVERALL=BLOCK
{ [ "$BACKEND" = "UNKNOWN" ] && [ "$FRONTEND" = "UNKNOWN" ]; } && OVERALL=WARN
echo "Backend: $BACKEND | Frontend: $FRONTEND | Overall: $OVERALL"

# Compact card for the Teams message (the CTO reads bullets, not essays);
# the full reports stay available behind the report link.
claude -p "$(cat qa/prompts/summarize-card.md)" --model "$MODEL" --dangerously-skip-permissions \
  > qa/out/card.md 2>/dev/null || true

if [ -n "${PUBLIC_URL:-}" ] && [ -n "${REPORTS_TOKEN:-}" ]; then
  RUN_URL="${PUBLIC_URL%/}/report?token=${REPORTS_TOKEN}"
else
  RUN_URL="https://github.com/${REPO}/compare/${BEFORE}...${AFTER}"
fi
export RUN_URL
OVERALL="$OVERALL" BACKEND="$BACKEND" FRONTEND="$FRONTEND" TESTS="SKIPPED (db-free audit)" \
  PUSHED_BY="${ACTOR:-unknown}" bash qa/scripts/post-teams.sh || echo "Teams post failed"

# ---- 7. Keep the reports for the /report endpoint
mkdir -p "$REPORTS_DIR"
rm -f "$REPORTS_DIR"/*
cp qa/out/requirements.md qa/out/backend-report.md qa/out/frontend-report.md qa/out/commits.txt qa/out/card.md "$REPORTS_DIR"/ 2>/dev/null || true
printf "%s %s\n" "${BEFORE:-}" "${AFTER:-}" > "$REPORTS_DIR/last-range.txt" 2>/dev/null || true
echo "=== audit complete: $OVERALL ==="
exit 0
