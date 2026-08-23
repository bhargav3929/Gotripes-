#!/usr/bin/env bash
# Fetch recent client meetings from Fathom and write them to qa/out/meeting.md.
#
# If a meeting title matches FATHOM_KEYWORD (default "gotrips") the newest match
# is used alone. Otherwise (common case: untitled "Impromptu ..." meetings) the
# newest MAX_CANDIDATES meetings are all included, and the requirements-extraction
# agent identifies which one is the GoTrips client call from the content.
#
# Requires: FATHOM_API_KEY (Fathom -> Settings -> API)
# Optional: FATHOM_KEYWORD (default gotrips), FATHOM_LOOKBACK_DAYS (default 14),
#           FATHOM_MAX_CANDIDATES (default 3)
set -euo pipefail

OUT_DIR="qa/out"
mkdir -p "$OUT_DIR"

if [ -z "${FATHOM_API_KEY:-}" ]; then
  echo "FATHOM_API_KEY not set — skipping meeting fetch."
  exit 0
fi

KEYWORD="${FATHOM_KEYWORD:-gotrips}"
LOOKBACK_DAYS="${FATHOM_LOOKBACK_DAYS:-14}"
MAX_CANDIDATES="${FATHOM_MAX_CANDIDATES:-3}"
API="https://api.fathom.ai/external/v1"
CREATED_AFTER=$(date -u -d "-${LOOKBACK_DAYS} days" +%Y-%m-%dT%H:%M:%SZ 2>/dev/null \
  || date -u -v-"${LOOKBACK_DAYS}"d +%Y-%m-%dT%H:%M:%SZ)

echo "Fetching Fathom meetings since $CREATED_AFTER (with transcripts)..."
DETAIL_JSON=$(curl -sf --max-time 180 \
  -H "X-Api-Key: $FATHOM_API_KEY" \
  "$API/meetings?created_after=$CREATED_AFTER&include_transcript=true&include_summary=true&include_action_items=true")

COUNT=$(echo "$DETAIL_JSON" | jq '.items | length')
if [ -z "$COUNT" ] || [ "$COUNT" = "0" ]; then
  echo "No Fathom meetings found in the last $LOOKBACK_DAYS days."
  exit 0
fi

# Prefer an exact title match on the keyword; otherwise take the newest N.
CANDIDATES=$(echo "$DETAIL_JSON" | jq --arg kw "$KEYWORD" --argjson n "$MAX_CANDIDATES" '
  ([.items[] | select((.title // .meeting_title // "") | ascii_downcase | contains($kw | ascii_downcase))]
   | sort_by(.created_at) | reverse) as $matched
  | if ($matched | length) > 0 then [$matched[0]]
    else (.items | sort_by(.created_at) | reverse | .[0:$n])
    end')

N=$(echo "$CANDIDATES" | jq 'length')
echo "Writing $N candidate meeting(s) to $OUT_DIR/meeting.md"

{
  echo "# Recent Fathom meetings ($N candidate(s))"
  echo ""
  echo "NOTE FOR THE ANALYST AGENT: if more than one meeting is below, first decide"
  echo "which one is the most recent CLIENT call about the GoTrips travel website"
  echo "(gotrips.ai — UAE activities/visas, eSIM, FIFA 2026 tickets, admin/manager"
  echo "panels) and extract requirements from THAT one only. State which you chose."
  echo ""
  i=0
  while [ "$i" -lt "$N" ]; do
    M=$(echo "$CANDIDATES" | jq ".[$i]")
    echo "---"
    echo "## Candidate $((i+1)): $(echo "$M" | jq -r '.title // .meeting_title // "Untitled meeting"')"
    echo "- Recorded: $(echo "$M" | jq -r '.created_at // ""')"
    echo "- Fathom link: $(echo "$M" | jq -r '.share_url // .url // ""')"
    echo ""
    echo "### Summary"
    echo "$M" | jq -r '.default_summary.markdown_formatted // .default_summary // "No summary available." | tostring'
    echo ""
    echo "### Action items"
    echo "$M" | jq -r '(.action_items // []) | if length == 0 then "None recorded." else map("- " + (.description // (.|tostring))) | join("\n") end'
    echo ""
    echo "### Transcript"
    echo "$M" | jq -r '(.transcript // []) | if type == "array" then map(((.speaker.display_name // .speaker_name // .speaker // "Speaker") + ": " + (.text // .transcript // ""))) | join("\n") else tostring end' \
      | head -c 120000
    echo ""
    i=$((i+1))
  done
} > "$OUT_DIR/meeting.md"

echo "Wrote $OUT_DIR/meeting.md ($(wc -c < "$OUT_DIR/meeting.md") bytes)"
