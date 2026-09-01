#!/usr/bin/env bash
# Fetch the most recent GoTrips CLIENT meeting from Fathom → qa/out/meeting.md
#
# Two-step selection (meetings are usually untitled "Impromptu ..." and the
# founder talks to several clients):
#   1. list every meeting in the last FATHOM_LOOKBACK_DAYS with its summary
#   2. a small AI pass picks the most recent meeting that is a client call about
#      the GoTrips travel website (by content — titles are useless)
#   3. fetch the full transcript of that one meeting only
# Fallback if the pick fails: newest FATHOM_MAX_CANDIDATES meetings with
# transcripts, and the extraction agent picks.
#
# Requires: FATHOM_API_KEY. Optional: FATHOM_LOOKBACK_DAYS (30),
# FATHOM_MAX_CANDIDATES (3), FATHOM_PICK_MODEL (claude-haiku-4-5-20251001),
# FATHOM_KEYWORD — if a meeting TITLE contains it, that meeting wins outright.
set -euo pipefail

OUT_DIR="qa/out"; mkdir -p "$OUT_DIR"
[ -z "${FATHOM_API_KEY:-}" ] && { echo "FATHOM_API_KEY not set — skipping meeting fetch."; exit 0; }

API="https://api.fathom.ai/external/v1"
LOOKBACK_DAYS="${FATHOM_LOOKBACK_DAYS:-30}"
MAX_CANDIDATES="${FATHOM_MAX_CANDIDATES:-3}"
PICK_MODEL="${FATHOM_PICK_MODEL:-claude-haiku-4-5-20251001}"
KEYWORD="${FATHOM_KEYWORD:-gotrips}"
CREATED_AFTER=$(date -u -d "-${LOOKBACK_DAYS} days" +%Y-%m-%dT%H:%M:%SZ 2>/dev/null \
  || date -u -v-"${LOOKBACK_DAYS}"d +%Y-%m-%dT%H:%M:%SZ)

echo "Listing Fathom meetings since $CREATED_AFTER (summaries only)..."
LIST=$(curl -sf --max-time 120 -H "X-Api-Key: $FATHOM_API_KEY" \
  "$API/meetings?created_after=$CREATED_AFTER&include_summary=true" | jq '.items | sort_by(.created_at) | reverse')
COUNT=$(echo "$LIST" | jq 'length')
[ "$COUNT" = "0" ] && { echo "No Fathom meetings in the last $LOOKBACK_DAYS days."; exit 0; }
echo "$COUNT meeting(s) found."

# --- step 2: choose the meeting
CHOSEN=$(echo "$LIST" | jq -r --arg kw "$KEYWORD" \
  '[.[] | select((.title // .meeting_title // "") | ascii_downcase | contains($kw | ascii_downcase))] | first | .recording_id // empty')
if [ -n "$CHOSEN" ]; then
  echo "Title keyword match → recording $CHOSEN"
else
  echo "$LIST" | jq -r '.[] | "### recording_id=\(.recording_id) | \(.created_at[0:10]) | \(.title // "untitled")\n\((.default_summary.markdown_formatted // .default_summary // "no summary") | tostring | .[0:1500])\n"' \
    > "$OUT_DIR/meeting-candidates.md"
  PICK_PROMPT="Below are summaries of recent meetings recorded by the GoTrips founder, who also works with other clients. Identify the MOST RECENT meeting that is a client call about the GoTrips travel website (gotrips.ai: UAE activities, UAE/global visas, eSIM, FIFA 2026 tickets, Umrah/packages, agent/partner registration, admin or manager panels). Reply with ONLY the recording_id number, nothing else. If none qualifies, reply NONE.

$(cat "$OUT_DIR/meeting-candidates.md")"
  CHOSEN=$(claude -p "$PICK_PROMPT" --model "$PICK_MODEL" --dangerously-skip-permissions 2>/dev/null \
    | grep -oE '[0-9]{6,}|NONE' | head -1 || true)
  if echo "$LIST" | jq -e --arg id "$CHOSEN" '[.[] | select((.recording_id|tostring) == $id)] | length > 0' > /dev/null 2>&1; then
    echo "AI pick → recording $CHOSEN ($(echo "$LIST" | jq -r --arg id "$CHOSEN" '.[] | select((.recording_id|tostring)==$id) | .created_at[0:10]'))"
  else
    echo "AI pick unavailable ('$CHOSEN') — falling back to newest $MAX_CANDIDATES meetings."
    CHOSEN=""
  fi
fi

# --- step 3: fetch transcript(s)
if [ -n "$CHOSEN" ]; then
  AT=$(echo "$LIST" | jq -r --arg id "$CHOSEN" '.[] | select((.recording_id|tostring)==$id) | .created_at')
  AFTER=$(date -u -d "$AT -2 minutes" +%Y-%m-%dT%H:%M:%SZ 2>/dev/null || date -u -j -v-2M -f "%Y-%m-%dT%H:%M:%SZ" "$AT" +%Y-%m-%dT%H:%M:%SZ)
  BEFORE=$(date -u -d "$AT +2 minutes" +%Y-%m-%dT%H:%M:%SZ 2>/dev/null || date -u -j -v+2M -f "%Y-%m-%dT%H:%M:%SZ" "$AT" +%Y-%m-%dT%H:%M:%SZ)
  CANDIDATES=$(curl -sf --max-time 180 -H "X-Api-Key: $FATHOM_API_KEY" \
    "$API/meetings?created_after=$AFTER&created_before=$BEFORE&include_transcript=true&include_summary=true&include_action_items=true" \
    | jq --arg id "$CHOSEN" '[.items[] | select((.recording_id|tostring)==$id)]')
  HEADER="Selected as the latest GoTrips client call out of $COUNT recent meetings."
else
  CANDIDATES=$(curl -sf --max-time 180 -H "X-Api-Key: $FATHOM_API_KEY" \
    "$API/meetings?created_after=$CREATED_AFTER&include_transcript=true&include_summary=true&include_action_items=true" \
    | jq --argjson n "$MAX_CANDIDATES" '.items | sort_by(.created_at) | reverse | .[0:$n]')
  HEADER="NOTE FOR THE ANALYST AGENT: several meetings below — first decide which is the most recent CLIENT call about the GoTrips travel website and extract from THAT one only. State which you chose."
fi

N=$(echo "$CANDIDATES" | jq 'length')
{
  echo "# Fathom meeting(s) ($N)"; echo ""; echo "$HEADER"; echo ""
  i=0
  while [ "$i" -lt "$N" ]; do
    M=$(echo "$CANDIDATES" | jq ".[$i]")
    echo "---"
    echo "## Candidate $((i+1)): $(echo "$M" | jq -r '.title // .meeting_title // "Untitled meeting"')"
    echo "- Recorded: $(echo "$M" | jq -r '.created_at // ""')"
    echo "- Fathom link: $(echo "$M" | jq -r '.share_url // .url // ""')"
    echo ""; echo "### Summary"
    echo "$M" | jq -r '.default_summary.markdown_formatted // .default_summary // "No summary available." | tostring'
    echo ""; echo "### Action items"
    echo "$M" | jq -r '(.action_items // []) | if length == 0 then "None recorded." else map("- " + (.description // (.|tostring))) | join("\n") end'
    echo ""; echo "### Transcript"
    echo "$M" | jq -r '(.transcript // []) | if type == "array" then map(((.speaker.display_name // .speaker_name // .speaker // "Speaker") + ": " + (.text // .transcript // ""))) | join("\n") else tostring end' | head -c 150000
    echo ""
    i=$((i+1))
  done
} > "$OUT_DIR/meeting.md"
echo "Wrote $OUT_DIR/meeting.md ($(wc -c < "$OUT_DIR/meeting.md") bytes, $N meeting(s))"
