# GoTrips QA Auditor — cloud QA on Railway (database-free)

When a **teammate** (not the founder) pushes to `main`, a small worker running on
**Railway** audits the release and posts a CTO report to **Teams**. It runs 24/7 in
Railway's cloud — no laptop needs to be on — and is billed to your existing Railway
subscription plus your **Claude Code subscription** (via `claude setup-token`, no
Anthropic API key needed).

**Database-free by design.** The auditor never boots the Laravel app, never runs
migrations or PHPUnit, and never connects to any database. It does exactly two
things: reads the pushed code (read-only clone), and browses the already-live
website in a real browser the way a visitor does.

```
teammate (e.g. Pragetty) pushes to GitHub
        │
        ├──► webhook-deploy.php auto-deploys to Hostinger → site goes LIVE (unchanged)
        │
        └──► GitHub pings the Railway worker (plain webhook — nothing runs on GitHub)
                │
                ▼  Railway container (always on, your Mac can be off)
        ┌─ QA Auditor ──────────────────────────────────────────────────┐
        │ 1. Read-only clone of the repo; extract the exact pushed diff │
        │ 2. Fetch latest GoTrips client call from Fathom API           │
        │    → Agent 1: testable requirements checklist                 │
        │ 3. Agent 2 (backend): code-review the diff — bugs, security,  │
        │    money paths — + mark each client ask DONE/PARTIAL/MISSING  │
        │ 4. Wait ~5 min for the Hostinger deploy to land               │
        │ 5. Agent 3 (frontend): real Chromium browser on the LIVE site │
        │    — 10–15 user personas, desktop + mobile, strict production │
        │    safety rules (no payments, no record-creating submissions, │
        │    read-only in admin)                                        │
        └───────────────────────────────────────────────────────────────┘
                │
                └─► Teams card to your team channel:
                    ✅ healthy / ⚠️ issues, team should review / 🚨 critical,
                    needs hotfix — who pushed, requirement coverage, findings,
                    button to the full report.
```

Founder pushes (`bhargav3929`) are skipped automatically (`SKIP_ACTORS`).

## One-time setup (~20 minutes)

### 1. Generate the tokens

- **Claude token** (on your Mac): `claude setup-token` → copy the token. This
  bills your Claude subscription; no API key involved.
- **GitHub token**: github.com → Settings → Developer settings → Fine-grained
  tokens → new token with access to only `Gotripes-`, permission **Contents:
  Read-only**. (Used to clone the private repo.)
- **Fathom key**: Fathom → Settings → API → generate.
- **Teams webhook**: in the team channel → `⋯` → Workflows → "Post to a channel
  when a webhook request is received" → copy the URL. (The channel you pick is
  where reports land.)
- Invent two random strings: `WEBHOOK_SECRET` and `REPORTS_TOKEN`
  (e.g. `openssl rand -hex 20`).

### 2. Deploy the worker to Railway

Deployed via the Railway CLI **from the local `qa-worker/` folder** (`railway init`
→ `railway up`), NOT linked to the GitHub repo. This is deliberate: teammate
pushes must trigger *audits*, not *rebuilds* of the worker (rebuilds would burn
Railway usage on every push). To update the worker itself later:
`cd qa-worker && railway up`.

It runs as its own project/service — always-on per the founder's choice (idle
footprint is a small Node process). This never affects other Railway services
(n8n etc.); app-sleeping, if ever wanted, is a per-service toggle
(`railway.json → deploy.sleepApplication`).

1. `cd qa-worker && railway init` (new project, e.g. `gotrips-qa`) → `railway up`
2. `railway domain` to generate the public URL (this is `PUBLIC_URL`)
3. Optional but recommended: `railway volume add -m /data` so the
   last-audited-commit marker and the latest report survive restarts
4. Variables (`railway variables --set "KEY=value"` or dashboard):

| Variable | Value |
|---|---|
| `CLAUDE_CODE_OAUTH_TOKEN` | from `claude setup-token` |
| `GITHUB_TOKEN` | the fine-grained read-only token |
| `WEBHOOK_SECRET` | your random string |
| `REPORTS_TOKEN` | your other random string |
| `FATHOM_API_KEY` | from Fathom |
| `TEAMS_WEBHOOK_URL` | from Teams Workflows |
| `PUBLIC_URL` | the service's public URL (next step) |
| `QA_ADMIN_EMAIL` / `QA_ADMIN_PASSWORD` | optional — a TEST admin login for panel checks |

Optional tuning variables: `SKIP_ACTORS` (default `bhargav3929`), `LIVE_URL`
(default `https://gotrips.ai`), `DEPLOY_WAIT_SECONDS` (default `300`),
`CLAUDE_QA_MODEL` (default `claude-sonnet-5`), `FATHOM_KEYWORD` (default
`gotrips`), `POLL_INTERVAL_MINUTES` (see below).

### 3. Point a GitHub webhook at it

Repo → Settings → Webhooks → Add webhook:
- Payload URL: `<PUBLIC_URL>/webhook`
- Content type: `application/json`
- Secret: your `WEBHOOK_SECRET`
- Events: **Just the push event**

This is only a notification ping — nothing executes on GitHub. If you prefer
zero GitHub configuration, skip this step and set `POLL_INTERVAL_MINUTES=5`
instead: the worker then checks the repo for new pushes itself.

## Day-to-day

- Nothing to do. Teammate pushes → site auto-deploys as usual → report arrives
  in Teams ~15–30 min later.
- Full report: the card's button opens `<PUBLIC_URL>/report?token=...`
  (requirements + backend + frontend reports as plain text).
- Status/history: `<PUBLIC_URL>/status`. Live logs: `railway logs`.
- Manual run: `curl -X POST "<PUBLIC_URL>/run?token=<REPORTS_TOKEN>"`.
- No client meeting this cycle? Write the asks into `qa/requirements-override.md`
  (numbered `[REQ-n]` style) and push it — used whenever Fathom has nothing.
- Local run from your Mac (unchanged): `qa/scripts/run-local.sh`.

## Files

| File | Purpose |
|---|---|
| `qa-worker/Dockerfile` | Railway container: Node + git + Claude CLI + Chromium |
| `qa-worker/server.js` | Webhook receiver / poller, job queue, report endpoint |
| `qa-worker/pipeline.sh` | The audit pipeline (clone → agents → Teams) |
| `qa-worker/mcp-playwright.json` | Browser tools config for the frontend agent |
| `qa/prompts/*.md` | The three agent briefs (requirements, backend, frontend) |
| `qa/scripts/fetch-fathom.sh` | Pulls the latest matching meeting from the Fathom API |
| `qa/scripts/post-teams.sh` | Posts the Adaptive Card report to Teams |
| `qa/scripts/run-local.sh` | Run the pipeline from your Mac instead |

## Cost

Railway bills actual vCPU/RAM usage per second. Idle, this worker is a small
Node process (~100 MB, near-zero CPU) — roughly $1–3/month. Each audit runs the
Claude CLI + a headless Chromium for ~15–30 minutes — a few cents per audited
push. Because the worker is deployed from the CLI (not repo-linked), teammate
pushes never trigger Railway builds, so there is no per-push build cost. The
audits themselves bill the founder's Claude Code subscription (via
`CLAUDE_CODE_OAUTH_TOKEN`), not a pay-per-token API account.

## Why Railway and not Vercel

The audit is a 15–30 minute job running git, a headless Chromium browser, and
long-lived agent processes. Railway runs a persistent container, which is exactly
that shape. Vercel runs short serverless functions with execution-time limits and
no room for a browser + CLI toolchain — not usable for this. (Vercel could host a
pretty report dashboard later if you ever want one; Teams + `/report` covers it
for now.)

## Honest limitations

- The frontend agent tests the LIVE site under strict safety rules (no payments,
  no record-creating form submissions, read-only in admin). That means final
  "submit" steps of flows are verified only up to the last safe step.
- Fathom matching is by meeting title keyword — keep "GoTrips" in client call
  titles or change `FATHOM_KEYWORD`.
- PHPUnit is intentionally NOT run in the cloud (the test config points at a real
  database — the exact risk this design avoids). Run tests locally before pushing.
- A 🚨 report means the release is already live with a critical problem — treat
  it as a hotfix signal to the team.
