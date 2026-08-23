#!/usr/bin/env bash
# Post the QA verdict to a Microsoft Teams channel via a Workflows webhook
# (Power Automate "When a Teams webhook request is received" flow).
# Expects env: TEAMS_WEBHOOK_URL, OVERALL, BACKEND, FRONTEND, TESTS, RUN_URL
set -euo pipefail

[ -z "${TEAMS_WEBHOOK_URL:-}" ] && { echo "TEAMS_WEBHOOK_URL not set — skipping."; exit 0; }

OVERALL="${OVERALL:-UNKNOWN}"
case "$OVERALL" in
  PASS)  COLOR="Good";      ICON="✅"; HEADLINE="Release is healthy — nothing needed from you" ;;
  WARN)  COLOR="Warning";   ICON="⚠️"; HEADLINE="Live, but issues found — team should review" ;;
  BLOCK) COLOR="Attention"; ICON="🚨"; HEADLINE="Critical issues in this release — needs a hotfix" ;;
  *)     COLOR="Warning";   ICON="❓"; HEADLINE="QA run incomplete — check the logs" ;;
esac

# Pull short excerpts out of the agent reports for the card body.
excerpt() { # file, max chars
  [ -s "$1" ] && head -c "${2:-1200}" < <(sed -n '/## KEY FINDINGS/,/## /p' "$1" | grep -v '^## ' | grep -v '^VERDICT:' | grep -v '^$' | head -20) || echo "n/a"
}
BACKEND_NOTES=$(excerpt qa/out/backend-report.md 1200)
FRONTEND_NOTES=$(excerpt qa/out/frontend-report.md 1200)
COVERAGE=$(sed -n '/## REQUIREMENTS COVERAGE/,/## /p' qa/out/backend-report.md 2>/dev/null | grep -v '^## ' | grep -v '^VERDICT:' | head -25 || true)
[ -z "$COVERAGE" ] && COVERAGE="No meeting requirements were checked this run."
COMMITS=$(head -5 qa/out/commits.txt 2>/dev/null || echo "n/a")

jq -n \
  --arg headline "$ICON GoTrips QA Report: $OVERALL — $HEADLINE" \
  --arg color "$COLOR" \
  --arg pushed_by "Pushed by: ${PUSHED_BY:-unknown}" \
  --arg backend "Backend code review: ${BACKEND:-?}" \
  --arg frontend "Frontend persona testing: ${FRONTEND:-?}" \
  --arg tests "PHPUnit suite: ${TESTS:-?}" \
  --arg commits "$COMMITS" \
  --arg coverage "$COVERAGE" \
  --arg backend_notes "$BACKEND_NOTES" \
  --arg frontend_notes "$FRONTEND_NOTES" \
  --arg run_url "${RUN_URL:-https://github.com}" \
  '{
    type: "message",
    attachments: [{
      contentType: "application/vnd.microsoft.card.adaptive",
      content: {
        "$schema": "http://adaptivecards.io/schemas/adaptive-card.json",
        type: "AdaptiveCard", version: "1.4",
        body: [
          { type: "TextBlock", size: "Large", weight: "Bolder", color: $color, wrap: true, text: $headline },
          { type: "TextBlock", wrap: true, spacing: "Small", text: ("**Commits under review:**\n" + $commits) },
          { type: "FactSet", facts: [
              { title: "Author", value: $pushed_by },
              { title: "Backend", value: $backend },
              { title: "Frontend", value: $frontend },
              { title: "Tests", value: $tests }
          ]},
          { type: "TextBlock", weight: "Bolder", text: "Meeting requirements coverage", spacing: "Medium" },
          { type: "TextBlock", wrap: true, text: $coverage },
          { type: "TextBlock", weight: "Bolder", text: "Backend findings", spacing: "Medium" },
          { type: "TextBlock", wrap: true, text: $backend_notes },
          { type: "TextBlock", weight: "Bolder", text: "Frontend findings", spacing: "Medium" },
          { type: "TextBlock", wrap: true, text: $frontend_notes }
        ],
        actions: [
          { type: "Action.OpenUrl", title: "Open full QA report", url: $run_url }
        ]
      }
    }]
  }' > qa/out/teams-payload.json

HTTP_CODE=$(curl -s -o qa/out/teams-response.txt -w "%{http_code}" \
  -H "Content-Type: application/json" \
  -d @qa/out/teams-payload.json \
  "$TEAMS_WEBHOOK_URL")
echo "Teams webhook responded HTTP $HTTP_CODE"
[ "$HTTP_CODE" -ge 200 ] && [ "$HTTP_CODE" -lt 300 ] || { cat qa/out/teams-response.txt; exit 1; }
