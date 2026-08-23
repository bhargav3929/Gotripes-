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

COMMITS=$(head -2 qa/out/commits.txt 2>/dev/null || echo "n/a")

# Preferred: the compact bullet card written by the summarizer agent.
# Fallback (if card.md is missing/empty): raw finding lines, trimmed hard.
if [ -s qa/out/card.md ]; then
  CARD=$(head -c 6000 qa/out/card.md)
else
  CARD=$(printf '**Backend blockers:**\n%s\n\n**Frontend blockers:**\n%s' \
    "$(grep -E '^\- \[BLOCKER' qa/out/backend-report.md 2>/dev/null | cut -c1-200 || echo '- None')" \
    "$(grep -E '^\- \[BLOCKER' qa/out/frontend-report.md 2>/dev/null | cut -c1-200 || echo '- None')")
fi

jq -n \
  --arg headline "$ICON GoTrips QA: $OVERALL — $HEADLINE" \
  --arg color "$COLOR" \
  --arg card "$CARD" \
  --arg footer "Pushed by: ${PUSHED_BY:-unknown} · $COMMITS" \
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
          { type: "TextBlock", wrap: true, text: $card },
          { type: "TextBlock", isSubtle: true, wrap: true, spacing: "Medium", text: $footer }
        ],
        actions: [
          { type: "Action.OpenUrl", title: "Full details", url: $run_url }
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
