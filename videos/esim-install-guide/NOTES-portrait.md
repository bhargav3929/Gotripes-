# Portrait (9:16) cut — DONE

Built 15 Sep 2026. Amer asked for "this 9:11 screen size too"; read as **9:16,
1080 x 1920**. Worth confirming the ratio with him, but 9:11 is not a real
screen shape and 9:16 is the standard phone/Reels/Status format.

## Where it lives

The portrait cut is a **separate project**: `videos/esim-install-guide-portrait/`.
A HyperFrames project may declare only one root composition, so it could not sit
beside `index.html` here. Its `assets/` is a symlink back to this project's
`assets/`, so the narration, music and brand logo are shared, not duplicated.

- Generator: `build-portrait.py` in THIS folder writes the sibling project.
  Re-run it after changing scene content, then `npm run check && npm run render`
  from the portrait project.
- Rendered file ships to `public/assets/esim/how-to-install-esim-portrait.mp4`.
- Linked from `resources/views/esim.blade.php` (under the landscape player) and
  `resources/views/emails/esim-qr.blade.php` (under the main button).

## How it was built

Not a letterbox of the wide cut. The ten scenes were laid out again for a tall
screen: headline at the top, the device diagram or cards stacked beneath, type
scaled up, and content kept above y=1340 because the captions track owns the
band at 1380-1580. Same narration files, same `data-start`/`data-duration` as
the landscape root, so the two cuts stay frame-for-frame in step.

Both cuts run 84.259s. Check passes with 0 errors and 43/43 contrast checks.

## The logo asset trap (still true)

Use `assets/brand/logo.png`. Do NOT swap in
`public/assets/index_files/transparent_logo.png` — despite the name it has no
alpha channel and the transparency checkerboard is painted into the pixels.
