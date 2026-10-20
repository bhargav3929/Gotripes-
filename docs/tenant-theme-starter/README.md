# Your GoTrips theme

This repository is the look and behaviour of your GoTrips site: the public
storefront your customers see, and the manager dashboard your staff use.

Push to `main` and your changes are live in about a minute. You don't need
to build anything or log into a server.

## What's in here

| File | What it does |
|---|---|
| `theme.json` | Your colours, fonts, logo and favicon, plus switches for the files below |
| `theme.css` | Styles for the storefront |
| `theme.js` | Scripts for the storefront |
| `dashboard.css` | Styles for the manager dashboard (`/manager`) |
| `dashboard.js` | Scripts for the manager dashboard |
| `assets/` | Images, fonts, videos and any other CSS/JS files you reference |

Every file is optional. Delete the ones you don't use.

## Publishing

1. Commit and push to `main`.
2. GitHub tells GoTrips, which downloads that commit, checks it, and makes it live.
3. To see the result, open the repository on GitHub: **Settings → Webhooks →
   (the GoTrips webhook) → Recent deliveries**. The response says either
   `Deployed abc1234` or exactly why the push was refused.

Pushes to other branches are ignored, so you can work on a branch and merge
when you're ready.

If a push is refused, the site keeps showing the previous version. Nothing is
half-published. GoTrips keeps your last five versions and can switch back to any of
them.

## theme.json

```json
{
  "name": "My travel brand",
  "version": "1.0.0",
  "logo": "assets/logo.svg",
  "favicon": "assets/favicon.png",
  "colors": {
    "primary": "#0E7C66",
    "secondary": "#F4B942",
    "accent": "#E4572E",
    "text": "#F5F3EE",
    "background": "#0B1412",
    "surface": "#13201D",
    "border": "#24352F"
  },
  "fonts": {
    "google": ["Bricolage Grotesque:wght@400;700", "Manrope:wght@300;500"],
    "heading": "Bricolage Grotesque",
    "body": "Manrope"
  },
  "storefront": { "css": true, "js": true },
  "dashboard": { "css": true, "js": true }
}
```

- **logo / favicon**: a file in `assets/`. Replaces the logo in the site
  header, the dashboard sidebar and the browser tab.
- **colors**: hex values only. Each becomes a CSS variable you can use
  anywhere: `var(--tenant-primary)`, `var(--tenant-background)`, and so on.
- **fonts.google**: up to 4 Google Fonts families, written as in the Google
  Fonts URL (`Family Name:wght@400;700`), so they're loaded on every page.
- **fonts.heading / fonts.body**: applied to all headings and body text on
  the storefront and dashboard automatically. They're also available as
  `var(--tenant-font-heading)` and `var(--tenant-font-body)`.
- **storefront / dashboard**: set `"js": false` (or `"css": false`) to switch
  a file off without deleting it.

Unknown keys are refused, so a typo is reported rather than silently ignored.

## Writing CSS that wins

Your CSS loads after the platform's, but some pages carry their own styles.
Start every selector with `html[data-gt-theme]`, which is set only when your
theme is live:

```css
html[data-gt-theme] .gt-nav-link:hover { color: var(--tenant-secondary); }
```

A few platform styles are marked `!important`: the page background, body
font and colour, and headings. To change one of those, add `!important` to
yours too:

```css
html[data-gt-theme] body { background-color: var(--tenant-background) !important; }
```

## Targeting one page

The `<html>` element carries three attributes:

| Attribute | Example |
|---|---|
| `data-gt-theme` | your company id, e.g. `savanna` |
| `data-gt-surface` | `storefront` or `dashboard` |
| `data-gt-page` | the page, see below |

Common storefront pages:

| Page | `data-gt-page` |
|---|---|
| Home | `/` |
| Activities | `emirates.index` |
| Activity details | `activities.detail` |
| UAE visa | `/uaevisa` |
| e-Visa | `visa.evisa.form` |
| eSIM | `esim.index` |
| Contact | `/contact` |

To find any other page's value, open it and run
`document.documentElement.dataset.gtPage` in the browser console.

```css
html[data-gt-page="esim.index"] .hero { display: none; }
```

## JavaScript

`theme.js` and `dashboard.js` load with `defer`, so the page is ready when
they run. `window.GoTripsTheme` gives you:

```js
GoTripsTheme.tenant               // "savanna"
GoTripsTheme.surface              // "storefront" or "dashboard"
GoTripsTheme.page                 // same as data-gt-page
GoTripsTheme.asset("assets/x.jpg") // full URL of a file in this repository
```

For safety, theme scripts do **not** run on:
- the sign-in, registration and password pages
- the payment result pages
- any page while a GoTrips administrator is viewing your site

Styles still apply on those pages where the shared header is used.

## Rules

These are checked on every push. Anything that breaks them is refused with a
message saying which file and why.

- Only the files listed above at the top level. Everything else goes in `assets/`.
- `assets/` may contain: css, js, json, map, png, jpg, jpeg, gif, webp, avif,
  svg, ico, woff, woff2, ttf, otf, mp4, webm, mp3, txt.
- No server-side files (`.php`, `.htaccess` and similar). They're refused,
  even inside `assets/`.
- File and folder names: letters, numbers, dots, dashes and underscores. No spaces.
- No symbolic links.
- At most 400 files, 8 MB per file, 40 MB in total.

These are not published but are fine to keep in the repository: `README.md`
and other `.md` files, dotfiles and folders (`.github/`, `.gitignore`),
`src/`, `docs/` and `node_modules/`. So you can keep source files such as
SCSS in `src/` and commit the built CSS.

## What a theme can't change

Prices, markups, commissions, which services you sell, bookings, payments
and customer data all stay on the GoTrips platform. If you need a change
there, ask your GoTrips contact.
