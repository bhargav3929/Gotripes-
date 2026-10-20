# Tenant developer themes

A tenant with its own developers gets a private GitHub repository holding
their theme: `theme.json`, CSS, JavaScript and assets for their storefront and
manager dashboard. When they push to `main`, it goes live on their subdomain
within a minute.

They never get access to the GoTrips repository, the server, the database or
any other tenant. Everything is switched off until a super admin turns it on,
and a super admin can turn it off, roll it back or delete it at any time.

- Code: `app/Services/TenantTheme/`, `app/Http/Controllers/TenantThemeWebhookController.php`,
  `app/Http/Controllers/SuperAdmin/TenantThemeController.php`, `config/tenant_themes.php`
- What tenant developers receive: `docs/tenant-theme-starter/` (copy it into their repository)
- Tests: `tests/Feature/TenantTheme/`

## How it works

```
tenant developer ──push──▶ their GitHub repo ──signed webhook──▶ /api/tenant-themes/{id}/webhook
                                                                   │
            downloads that commit (GitHub API, platform token) ◀───┘
            checks every file (types, paths, sizes, theme.json)
            unpacks to public/tenant-themes/{company id}/{version}/
            switches the company to the new version
                                                                   │
<company>.gotrips.ai ◀── header + manager layout include theme.css / theme.js
```

- **Only static files are published.** CSS, JS, JSON, images, fonts and media.
  Anything a web server could execute (`.php`, `.phtml`, `.htaccess`, ...) is
  refused, and `public/tenant-themes/.htaccess` additionally switches off PHP
  and directory listing there.
- **Each company has its own webhook secret.** A push signed for one company
  can never deploy to another, and only the repository set on the company can deploy.
- **A refused or failed push changes nothing.** The new version is unpacked
  into a fresh folder and only switched on once every check passes.
- **The last 5 versions are kept** for one-click rollback.
- **Theme JavaScript never runs:**
  - for a super admin or while impersonating
  - on sign-in, registration and password pages (they don't use the shared layouts)
  - on payment result pages
- **Where the theme loads:** the storefront (`resources/views/header.blade.php`,
  used by every storefront page, plus `lotus-cruise-dubai`) and the manager
  dashboard (`resources/views/layouts/manager.blade.php`). The agent portal is
  not themed.

## One-time setup (production)

1. **Create a GitHub organisation for tenant themes,** for example `gotrips-themes`.
   Tenant repositories live there, separate from `bhargav3929/Gotripes-`.
2. **Create a fine-grained personal access token** (GitHub → Settings →
   Developer settings → Fine-grained tokens):
   - Resource owner: the themes organisation
   - Repository access: All repositories (or pick them one by one)
   - Permissions: **Contents: Read-only**. Nothing else.
3. **Add it to the server's `.env`:**
   ```
   TENANT_THEMES_GITHUB_TOKEN=github_pat_...
   ```
   Then run `php artisan config:clear`. Public repositories work without a
   token; private ones need it.
4. **Deploy as usual.** `deploy.sh` runs the migration that adds the
   `theme_deployments` table and the theme columns on `companies`.

## Giving a tenant developer access

1. **Create the repository** in the themes organisation, e.g.
   `gotrips-themes/savanna`. Make it **private** and copy in the contents of
   `docs/tenant-theme-starter/`.
2. **Add the tenant's developer(s)** to that repository only (Settings →
   Collaborators → Add people → role **Write**). Never add them to the organisation
   as members with access to other repositories.
3. **In Super Admin → Companies → (the company) → Developer Theme:**
   - Turn **Developer theme enabled** on.
   - Set **GitHub repository** to `gotrips-themes/savanna` and **Branch** to `main`. Save.
   - Click **Generate secret**.
4. **In the repository on GitHub,** go to Settings → Webhooks → Add webhook:
   - Payload URL and Secret: copy them from the Developer Theme page.
   - Content type: **application/json**.
   - Events: **Just the push event**.
   - Save. GitHub sends a ping, which should get a green tick.
5. **Click Deploy latest** on the Developer Theme page (or push to `main`).
   The History table shows the result.
6. **Send the developer the repository link.** `README.md` in the repository
   explains everything else.

A tenant without GitHub can send a zip instead. Upload it on the same page; it
goes through the same checks.

## Taking access away

| What you want | Do this | Effect |
|---|---|---|
| Hide the theme now | Developer Theme → switch **off** → Save | Next page load shows the standard look. Pushes are refused. |
| Undo a bad change | Developer Theme → History → **Make live** on an earlier version | That version is live at once. |
| Remove their files | Developer Theme → **Wipe** | All published files deleted; standard look. History kept. |
| Stop one developer | GitHub → repository → Collaborators → remove them | They can no longer push. |
| Stop all pushes, keep the current look | **Rotate secret** and don't give it to GitHub | Pushes fail the signature check. |
| Suspend the tenant | Company page → **Deactivate** | The whole subdomain is blocked; pushes are refused. |
| Delete the tenant | Company page → **Delete** | Theme files are wiped and the secret is cleared, as part of the delete. |

Every Developer Theme action is recorded in the super admin audit log
(`company.theme.*`).

## What tenants can and can't change

**Can:**
- Colours, fonts, logo and favicon
- Restyling anything on their storefront and dashboard
- Hiding, moving or adding elements with CSS/JS
- Their own analytics and chat widgets

**Can't:**
- Prices, markups, commissions or features
- Bookings, payments or customer records
- Any other tenant, the GoTrips main site or Super Admin
- Any server-side code

Their JavaScript does run in their own customers' and staff's browsers on their
subdomain, which is the same trust Shopify gives a merchant's theme. It can't
read other subdomains' cookies, because session cookies are host-only
(`SESSION_DOMAIN` is unset). Keep `SESSION_DOMAIN` unset. If it's ever set to
`.gotrips.ai`, tenant scripts could read sessions from other subdomains.

## Troubleshooting

The History table on the Developer Theme page and GitHub's "Recent deliveries"
both show the exact reason a push wasn't deployed.

| Message | Fix |
|---|---|
| Invalid signature | The secret in GitHub doesn't match. Paste it again, or rotate it and paste the new one. |
| could not find branch "main" | Check the repository name and branch, and that the token can read the repository. |
| GitHub refused the request (HTTP 401/403) | The token is missing, expired or lacks Contents: Read-only on that repository. |
| the developer theme is switched off | Switch it on in Developer Theme. |
| X is not allowed: themes cannot contain server-side files | Remove that file from the repository. |
| X is not allowed at the top of the theme | Move it into `assets/`. |
| theme.json ... | The message names the field and the allowed values. |
| Another deploy for this company is already running | Wait a minute and push again, or click Deploy latest. |

## Limits

These are set in `config/tenant_themes.php`:
- at most 400 files
- 8 MB per file
- 40 MB unpacked
- 50 MB zip
- 5 versions kept
