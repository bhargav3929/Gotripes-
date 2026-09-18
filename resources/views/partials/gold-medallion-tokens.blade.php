{{--
  Brand medallion tokens — the single definition of the "black disc, brushed
  gold ring" look the client asked for (Sep 2026). Included by the Emirates
  route selector and the partner/customer registration popup so both rings
  are painted from the same gradient and the same thickness ratio.

  --gm-ring-ratio: outer ring width as a fraction of the disc.
  History: 2.2% read too thin next to the logo (round 1), so it went to 8.5%;
  the client then found 8.5% heavy beside the thin Dubai/Sharjah rings
  (round 2, 17 Sep 2026). 4.5% frames the disc without dominating it; the
  small circles carry 10%, the same ratio as the ring baked into the logo.
--}}
<style>
:root{
  --gm-gold:#d4af37;
  --gm-gold-lite:#f0cf6b;
  --gm-gold-deep:#8f6a16;
  --gm-ring-ratio:.045;
  --gm-small-ring-ratio:.10;
  --gm-gold-metal:conic-gradient(from 210deg at 50% 50%,#f7e39a 0deg,#d4af37 55deg,#9a7418 120deg,#e6c65a 180deg,#f7e39a 220deg,#b8922a 290deg,#f7e39a 360deg);
  /* Small rings are only a few pixels wide; the full metal gradient's dark
     stops swallow them at that size, so they get a lighter sweep. */
  --gm-gold-bright:conic-gradient(from 200deg at 50% 50%,#fbe9a8 0deg,#e2bd4b 70deg,#c89e33 140deg,#f3d774 210deg,#fbe9a8 280deg,#d9b243 330deg,#fbe9a8 360deg);
  --gm-disc-fill:radial-gradient(circle at 50% 40%,#161616 0,#080808 60%,#000 100%);
}
</style>
