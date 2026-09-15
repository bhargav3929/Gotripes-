# Portrait (9:16) version — what is done and what is left

Amer asked on 15 Sep 2026 for "this 9:11 screen size too". Read as **9:16 portrait,
1080 x 1920** (9:11 is not a real screen ratio). Confirm with him if it matters.

## Done

- **Logo intro and outro** (`compositions/frames/00-logo-intro.html`, `09-logo-outro.html`)
  are in the landscape composition. Root is now 84.259 s; every original scene and its
  voice track was shifted by the 2.5 s intro. `hyperframes check` passes with 0 errors.
- **Brand asset** `assets/brand/logo.png` — 1024 x 1024 with a real alpha channel.
  Do NOT replace it with `public/assets/index_files/transparent_logo.png`: despite the
  name, that file has **no alpha** and the transparency checkerboard is painted into the
  pixels, so it renders as a grey chequered ring on any background. The clean version was
  built by masking that file to its circle and is also saved at
  `public/assets/index_files/logo-circle-1024.png`.
- **Portrait captions track** `compositions/captions-portrait.html` (1080 x 1920) is written.
- Landscape render shipped to `public/assets/esim/how-to-install-esim.mp4`.

## Left to do

1. Ten scene frames in `compositions/frames-portrait/` (the directory exists and is empty):
   `00-logo-intro` … `09-logo-outro`, each 1080 x 1920.
2. A root `index-portrait.html`: copy `index.html`, set `data-width="1080"`
   `data-height="1920"`, point every `data-composition-src` at `frames-portrait/`,
   and swap the captions source to `captions-portrait.html`. Keep every `data-start`
   and `data-duration` exactly as the landscape root has them, and reuse the same
   `assets/voice/*.wav` and `assets/bgm/track*.mp3`.
3. Render: `npx --yes hyperframes@0.8.31 render index-portrait.html`, then copy the
   output to `public/assets/esim/how-to-install-esim-portrait.mp4`.
4. Link it: a secondary "phone-friendly version" link under the `<video>` in
   `resources/views/esim.blade.php` (~line 4590) and beside the existing button in
   `resources/views/emails/esim-qr.blade.php` (~line 83).

## How much work each frame is

The frames are not uniformly hard, so check before estimating:

| Frame | Layout basis | Effort |
|---|---|---|
| 00, 09 (logo cards) | centred, container units | trivial, near copy |
| 01, 02, 08 | mostly `cqw` / `cqh` container units | light, mainly type scale and stacking |
| 03, 04, 05, 06, 07 | hand-positioned pixels (frame 03 alone has 93 px values) | real re-layout work |

Portrait rules to follow: stack the device diagram above the copy rather than side by
side, scale type up about 1.6x, keep the caption band in the lower third, and leave at
least 80 px clear top and bottom for social UI overlays. Do not letterback the 16:9
render into a 9:16 frame: the point of the portrait cut is that the text is readable on
a phone, and scaling the wide layout down makes it smaller, not larger.
