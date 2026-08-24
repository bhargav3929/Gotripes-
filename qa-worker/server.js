// GoTrips QA Auditor — Railway worker.
//
// Listens for GitHub push webhooks (or polls the repo if POLL_INTERVAL_MINUTES
// is set), skips pushes by the founder, and runs pipeline.sh: backend code
// review + live-site frontend testing by Claude agents, cross-checked against
// the latest Fathom client meeting, with the report posted to Teams.
//
// Required env:
//   CLAUDE_CODE_OAUTH_TOKEN  from `claude setup-token` (bills your Claude subscription)
//   GITHUB_TOKEN             fine-grained PAT, read-only Contents access to the repo
//   WEBHOOK_SECRET           shared secret for the GitHub webhook (any random string)
// Recommended env:
//   FATHOM_API_KEY, TEAMS_WEBHOOK_URL
// Optional env:
//   REPO (default bhargav3929/Gotripes-), SKIP_ACTORS (default bhargav3929),
//   LIVE_URL (default https://gotrips.ai), DEPLOY_WAIT_SECONDS (default 300),
//   CLAUDE_QA_MODEL (default claude-sonnet-5), FATHOM_KEYWORD (default gotrips),
//   POLL_INTERVAL_MINUTES (default 0 = webhook only), REPORTS_TOKEN (enables
//   GET /report), PUBLIC_URL (this service's URL, used for report links),
//   QA_ADMIN_EMAIL / QA_ADMIN_PASSWORD (test creds for admin-panel checks)

const express = require('express');
const crypto = require('crypto');
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 8080;
const REPO = process.env.REPO || 'bhargav3929/Gotripes-';
const SKIP_ACTORS = (process.env.SKIP_ACTORS || 'bhargav3929')
  .split(',').map(s => s.trim().toLowerCase()).filter(Boolean);
const WEBHOOK_SECRET = process.env.WEBHOOK_SECRET || '';
// AUTO_AUDIT=off: pushes are recorded but audits run ONLY via the founder's
// /start link. The audit then covers everything since the last audited commit.
const AUTO_AUDIT = (process.env.AUTO_AUDIT || 'on').toLowerCase() !== 'off';
// Use a Railway volume at /data when present so state and the last report
// survive restarts/redeploys; otherwise fall back to /tmp.
const DATA_DIR = fs.existsSync('/data') ? '/data' : '/tmp';
const REPORTS_DIR = path.join(DATA_DIR, 'qa-last-run');
const STATE_FILE = path.join(DATA_DIR, 'qa-last-sha.txt');

app.use(express.json({
  limit: '5mb',
  verify: (req, _res, buf) => { req.rawBody = buf; },
}));

// ---- single-flight job queue: run one audit at a time, keep only the latest pending
let running = null;   // {actor, after, startedAt}
let pending = null;   // latest job that arrived while busy
const history = [];   // last 20 job summaries

function verdictOf(report) {
  const m = (report || '').match(/VERDICT: *(PASS|WARN|BLOCK)/g);
  return m ? m[m.length - 1].replace(/VERDICT: */, '') : 'UNKNOWN';
}

function runJob(job) {
  if (running) { pending = job; console.log(`[queue] busy — queued push by ${job.actor}`); return; }
  running = { ...job, startedAt: new Date().toISOString() };
  console.log(`[job] starting audit: push by ${job.actor} (${job.before?.slice(0, 7)}..${job.after?.slice(0, 7)}) — ${job.reason}`);

  const child = spawn('bash', [path.join(__dirname, 'pipeline.sh')], {
    env: {
      ...process.env,
      REPO,
      BEFORE: job.before || '',
      AFTER: job.after || '',
      ACTOR: job.actor || 'unknown',
      REPORTS_DIR,
    },
    stdio: ['ignore', 'pipe', 'pipe'],
  });
  child.stdout.on('data', d => process.stdout.write(`[pipeline] ${d}`));
  child.stderr.on('data', d => process.stderr.write(`[pipeline] ${d}`));
  child.on('close', code => {
    const summary = {
      ...running,
      finishedAt: new Date().toISOString(),
      exitCode: code,
      backend: safeVerdict('backend-report.md'),
      frontend: safeVerdict('frontend-report.md'),
    };
    history.unshift(summary);
    history.length = Math.min(history.length, 20);
    if (job.after) {
      try { fs.writeFileSync(STATE_FILE, job.after); } catch (e) { console.error('[state]', e.message); }
    }
    console.log(`[job] finished (exit ${code}):`, JSON.stringify(summary));
    running = null;
    if (pending) { const next = pending; pending = null; runJob(next); }
  });
}

function safeVerdict(file) {
  try { return verdictOf(fs.readFileSync(path.join(REPORTS_DIR, file), 'utf8')); }
  catch { return 'UNKNOWN'; }
}

// ---- GitHub push webhook
app.post('/webhook', (req, res) => {
  if (WEBHOOK_SECRET) {
    const sig = req.get('X-Hub-Signature-256') || '';
    const expected = 'sha256=' + crypto.createHmac('sha256', WEBHOOK_SECRET)
      .update(req.rawBody || Buffer.alloc(0)).digest('hex');
    const ok = sig.length === expected.length &&
      crypto.timingSafeEqual(Buffer.from(sig), Buffer.from(expected));
    if (!ok) return res.status(403).send('bad signature');
  }
  const event = req.get('X-GitHub-Event');
  if (event === 'ping') return res.send('pong');
  if (event !== 'push') return res.send(`ignored event: ${event}`);

  const p = req.body || {};
  if (p.ref !== 'refs/heads/main') return res.send('not main, ignored');
  const actor = (p.pusher?.name || p.sender?.login || 'unknown').toLowerCase();
  if (p.deleted) return res.send('branch delete, ignored');
  if (!AUTO_AUDIT) {
    console.log(`[webhook] push by ${actor} (${(p.after || '').slice(0, 7)}) recorded — auto-audit is OFF, waiting for founder /start`);
    return res.send('recorded — auto-audit off, waiting for manual start');
  }
  if (SKIP_ACTORS.includes(actor)) return res.send(`push by ${actor} — founder push, audit skipped`);

  runJob({ actor, before: p.before, after: p.after, reason: 'webhook' });
  res.send('audit queued');
});

// ---- catch-up check: compare repo head to the last audited sha.
// Used by the optional poller AND once at startup, so pushes that landed while
// the worker was restarting/redeploying are still audited (the diff range
// last-audited..head covers everything missed in between).
async function checkForNewPush(reason) {
  if (!process.env.GITHUB_TOKEN) return;
  try {
    const r = await fetch(`https://api.github.com/repos/${REPO}/commits/main`, {
      headers: {
        Authorization: `Bearer ${process.env.GITHUB_TOKEN}`,
        'User-Agent': 'gotrips-qa-worker',
        Accept: 'application/vnd.github+json',
      },
    });
    if (!r.ok) { console.error(`[${reason}] GitHub API ${r.status}`); return; }
    const head = await r.json();
    if (!head.sha) return;
    const last = fs.existsSync(STATE_FILE) ? fs.readFileSync(STATE_FILE, 'utf8').trim() : '';
    if (head.sha === last) return;
    if (!last) {
      fs.writeFileSync(STATE_FILE, head.sha);
      console.log(`[${reason}] first run — baseline ${head.sha.slice(0, 7)} recorded, no audit`);
      return;
    }
    if (!AUTO_AUDIT) {
      console.log(`[${reason}] new commits up to ${head.sha.slice(0, 7)} — auto-audit is OFF, waiting for founder /start`);
      return;
    }
    const actor = (head.author?.login || head.commit?.author?.name || 'unknown').toLowerCase();
    if (SKIP_ACTORS.includes(actor)) {
      fs.writeFileSync(STATE_FILE, head.sha);
      console.log(`[${reason}] new push by ${actor} — founder, skipped`);
      return;
    }
    runJob({ actor, before: last, after: head.sha, reason });
  } catch (e) { console.error(`[${reason}] error:`, e.message); }
}

// Optional polling fallback (no GitHub webhook config needed)
const pollMinutes = parseFloat(process.env.POLL_INTERVAL_MINUTES || '0');
if (pollMinutes > 0) {
  setInterval(() => checkForNewPush('poll'), pollMinutes * 60 * 1000);
  console.log(`[poll] polling ${REPO} every ${pollMinutes} min`);
}
// One catch-up pass shortly after every (re)start
setTimeout(() => checkForNewPush('startup-catchup'), 10 * 1000);

// ---- founder's Start button: audits EVERYTHING since the last audited commit.
// GET so it works as a plain link pinned in the Teams channel.
async function startAudit(req, res) {
  if (!process.env.REPORTS_TOKEN || req.query.token !== process.env.REPORTS_TOKEN) {
    return res.status(403).send('forbidden');
  }
  const page = (msg) => `<!doctype html><meta name="viewport" content="width=device-width,initial-scale=1">
    <body style="font-family:-apple-system,sans-serif;background:#0f1115;color:#eee;display:grid;place-items:center;height:95vh">
    <div style="max-width:420px;text-align:center"><h2>GoTrips QA Auditor</h2><p style="font-size:1.1rem">${msg}</p></div></body>`;
  if (running) return res.send(page('⏳ An audit is already running — the report will arrive in Teams when it finishes.'));
  try {
    const r = await fetch(`https://api.github.com/repos/${REPO}/commits/main`, {
      headers: {
        Authorization: `Bearer ${process.env.GITHUB_TOKEN}`,
        'User-Agent': 'gotrips-qa-worker',
        Accept: 'application/vnd.github+json',
      },
    });
    if (!r.ok) return res.status(502).send(page(`GitHub API error ${r.status} — try again in a minute.`));
    const head = await r.json();
    const last = fs.existsSync(STATE_FILE) ? fs.readFileSync(STATE_FILE, 'utf8').trim() : '';
    if (last && head.sha === last) {
      return res.send(page('✅ Nothing new to audit — no commits since the last audit.'));
    }
    if (req.query.dry === '1') {
      return res.json({ wouldAudit: { before: last || '(last commit only)', after: head.sha } });
    }
    // Announce the start in the Teams channel (fire-and-forget)
    if (process.env.TEAMS_WEBHOOK_URL) {
      fetch(process.env.TEAMS_WEBHOOK_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          type: 'message',
          attachments: [{
            contentType: 'application/vnd.microsoft.card.adaptive',
            content: {
              $schema: 'http://adaptivecards.io/schemas/adaptive-card.json',
              type: 'AdaptiveCard', version: '1.4',
              body: [{ type: 'TextBlock', wrap: true, weight: 'Bolder',
                text: `🚀 QA audit started by the founder — covering all pushes since the last audit (up to ${head.sha.slice(0, 7)}). Report lands here in ~20 min.` }],
            },
          }],
        }),
      }).catch(e => console.error('[start] Teams announce failed:', e.message));
    }
    runJob({ actor: 'founder-start', before: last, after: head.sha, reason: 'manual-start' });
    res.send(page('🚀 Audit started! It covers every push since the last audit. The report will arrive in the Teams channel in about 20 minutes.'));
  } catch (e) {
    res.status(500).send(page(`Error: ${e.message}`));
  }
}
app.get('/start', startAudit);
app.post('/start', startAudit);

// ---- manual trigger + status + report viewing
app.post('/run', (req, res) => {
  if (!process.env.REPORTS_TOKEN || req.query.token !== process.env.REPORTS_TOKEN) {
    return res.status(403).send('set REPORTS_TOKEN and pass ?token=');
  }
  runJob({ actor: 'manual', before: '', after: '', reason: 'manual' });
  res.send('audit queued');
});

app.get('/status', (_req, res) => res.json({ running, pending: !!pending, history }));
app.get('/healthz', (_req, res) => res.send('ok'));

app.get('/report', (req, res) => {
  if (!process.env.REPORTS_TOKEN || req.query.token !== process.env.REPORTS_TOKEN) {
    return res.status(403).send('forbidden');
  }
  const parts = [];
  for (const f of ['requirements.md', 'backend-report.md', 'frontend-report.md', 'commits.txt']) {
    const fp = path.join(REPORTS_DIR, f);
    if (fs.existsSync(fp)) parts.push(`\n\n===== ${f} =====\n\n${fs.readFileSync(fp, 'utf8')}`);
  }
  res.type('text/plain').send(parts.join('') || 'no report yet');
});

app.listen(PORT, () => console.log(`GoTrips QA worker listening on :${PORT} (repo ${REPO}, skipping: ${SKIP_ACTORS.join(', ')})`));
