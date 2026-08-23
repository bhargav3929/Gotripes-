You are writing the short Teams notification card for the GoTrips founder/CTO. They will NOT read long reports — this card must be scannable in 20 seconds. Read `qa/out/requirements.md`, `qa/out/backend-report.md`, and `qa/out/frontend-report.md`, then output PLAIN TEXT in EXACTLY this structure and nothing else (no preamble, no code fences):

**Problem:** <one sentence, max 25 words: the single most important takeaway. If there are no blockers: "No critical problems in this release.">

**Client asked (from the meeting):**
- <emoji> <requirement in max 10 words>
(one bullet per requirement from the checklist; ✅ = done, ⚠️ = partly done, ❌ = missing or broken, ➖ = couldn't be verified. When the live-site test contradicts the code review, trust the live-site result. If no meeting requirements were available this run, write "- No client meeting this cycle — general QA only.")

**Backend blockers:**
- <max 20 words each, plain language, no file/line citations>
(or "- None")

**Frontend blockers:**
- <max 20 words each, describe what a customer experiences>
(or "- None")

**Also worth knowing:**
- <top warnings only, max 3 bullets, max 15 words each>
(omit this whole section if there are no warnings)

Hard rules: bullets only, no paragraphs, no headings other than the bold labels above, no code citations, total output under 220 words. Do not soften findings — ❌ means ❌.
