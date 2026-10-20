# Deploying gotrips.ai

**GitHub's `main` branch is the only source of truth for the live site.**

```
your computer ──commit──▶ push to main ──GitHub Actions──SSH──▶ deploy.sh on the server ──▶ live
```

1. Make the change on your computer and commit it.
2. Push (or merge a pull request) to `main`.
3. The **Deploy to Production** workflow (`.github/workflows/deploy.yml`)
   connects to the server over SSH and runs `deploy.sh`. In about a minute
   the server is on exactly that commit:
   - the code is updated
   - the caches are cleared
   - the migrations are run
4. Check the result in GitHub → **Actions**:
   - **Green tick:** it's live.
   - **Red X:** it isn't, and the log says why. GitHub also emails you about failed runs.

Every day at 08:30 Dubai time the same workflow runs a read-only check. It
goes red if the live code no longer matches GitHub. You can also run it any
time: **Actions → Deploy to Production → Run workflow** (`check` or `deploy`).

The workflow's SSH key (`DEPLOY_SSH_KEY` secret) can't open a shell. The
server's `~/.ssh/authorized_keys` pins it to `deploy-entry.sh`, which only
accepts `deploy` or `check`.

Hostinger blocks PHP from running commands, so there is deliberately no
deploy webhook. Deploys only happen through this workflow or by hand over
SSH (`bash deploy.sh`).

## Never edit or upload code on the server

No FTP uploads, no File Manager edits and no `git commit` on the server.
From 19 Aug to 21 Oct 2026 that's exactly what happened:
- an FTP workflow re-uploaded files on every push, and each file briefly
  vanished while it was replaced;
- every real deploy failed, while two webhooks still said "Deploy completed";
- the live site drifted two months away from GitHub. Fixes existed only on
  the server, and the Help pages broke without anyone noticing.

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

- **`.env`**: server settings and secrets, such as `TENANT_THEMES_GITHUB_TOKEN`.
- **Files uploaded through the panels**: `public/uploads/`, and the
  timestamped images under `public/assets/{homepageads,activities,packages}`.
  These are listed in `.gitignore`.
- **Tenant developer themes** in `public/tenant-themes/`. See `docs/TENANT_THEMES.md`.
- **`storage/`**: logs, sessions and cache.

## Backups

Before any risky change, back up the code and the database into
`~/site-backups/<date>/` on the server. The 21 Oct 2026 reconciliation backup
is in `~/site-backups/2026-10-21-pre-reconcile/`.

## The deploy key (one-time setup, done 21 Oct 2026)

- **GitHub:** the secret `DEPLOY_SSH_KEY` (repo → Settings → Secrets and variables → Actions)
  holds a private ed25519 key. No other copy exists.
- **Server:** its public half is in `~/.ssh/authorized_keys`, pinned like this:
  ```
  command="bash ~/domains/gotrips.ai/public_html/deploy-entry.sh",no-pty,no-port-forwarding,no-agent-forwarding,no-X11-forwarding ssh-ed25519 AAAA... github-actions-deploy@gotrips
  ```
- **To rotate it:**
  1. Generate a new key with `ssh-keygen -t ed25519 -N ""`.
  2. Replace that line on the server with the new public key, keeping the `command=...` part.
  3. Run `gh secret set DEPLOY_SSH_KEY < newkey`.
  4. Delete the local private key.
- **If the server's SSH host key ever changes** (Hostinger migration),
  update the pinned `known_hosts` line in `.github/workflows/deploy.yml`.
  Check the new fingerprint in hPanel first.
