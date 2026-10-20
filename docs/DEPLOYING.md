# Deploying gotrips.ai

**GitHub's `main` branch is the only source of truth for the live site.**

```
your computer ──commit──▶ push to main ──webhook──▶ deploy.sh on the server ──▶ live
```

1. Make the change on your computer and commit it.
2. Push (or merge a pull request) to `main`.
3. GitHub calls `https://gotrips.ai/webhook-deploy.php`, which starts
   `deploy.sh`. In about a minute the server is on exactly that commit:
   - the code is updated
   - the caches are cleared
   - the migrations are run
4. Open the Super Admin dashboard. If there's no red "not in sync with
   GitHub" warning, it worked. The full log is in `storage/logs/deploy.log`.

## Never edit or upload code on the server

No FTP uploads, no File Manager edits and no `git commit` on the server.
From 19 Aug to 21 Oct 2026 that's exactly what happened. Every automatic
deploy failed, the webhook still said "Deploy completed", and the live site
drifted two months away from GitHub. Fixes existed only on the server, and
the Help pages broke without anyone noticing.

`deploy.sh` now refuses to deploy over any file changed on the server. It
lists the files, saves their changes to `storage/logs/server-edits-*.patch`,
and the Super Admin dashboard shows a red warning naming them.

## If the warning appears

| Warning says | Do this |
|---|---|
| N file(s) were edited on the server | Copy the changes from `storage/logs/server-edits-*.patch` (or the files) into your local copy, commit and push. Then, over SSH in `public_html`: `git checkout -- . && git clean -fd -- app bootstrap config database resources routes && bash deploy.sh` |
| GitHub main has commits that are not deployed yet | Over SSH: `cd ~/domains/gotrips.ai/public_html && bash deploy.sh` |
| The server has commits that are not on GitHub | Push anything worth keeping from the server first, then `git reset --hard origin/main && bash deploy.sh` |
| composer / migrations / could not fetch | Read the end of `storage/logs/deploy.log`; fix, push, or rerun `bash deploy.sh` |

`bash deploy.sh --check` changes nothing. It just records whether the server
matches GitHub.

## Things deploys never touch

- **`.env`**: server settings and secrets, including `DEPLOY_WEBHOOK_SECRET`
  and `TENANT_THEMES_GITHUB_TOKEN`.
- **Files uploaded through the panels**: `public/uploads/`, and the
  timestamped images under `public/assets/{homepageads,activities,packages}`.
  These are listed in `.gitignore`.
- **Tenant developer themes** in `public/tenant-themes/`. See `docs/TENANT_THEMES.md`.
- **`storage/`**: logs, sessions and cache.

## Backups

Before any risky change, back up the code and the database into
`~/site-backups/<date>/` on the server. The 21 Oct 2026 reconciliation backup
is in `~/site-backups/2026-10-21-pre-reconcile/`.
