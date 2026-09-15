{{--
  Brand medallion tokens — the single definition of the "black disc, brushed
  gold ring" look the client asked for (Sep 2026). Included by the Emirates
  route selector and the partner/customer registration popup so both rings
  are painted from the same gradient and the same thickness ratio.

  --gm-ring-ratio: ring width as a fraction of the disc. The company logo's
  ring measures ~10% of its diameter; .085 plus the inner hairline reads as
  the same weight on screen.
--}}
<style>
:root{
  --gm-gold:#d4af37;
  --gm-gold-lite:#f0cf6b;
  --gm-gold-deep:#8f6a16;
  --gm-ring-ratio:.085;
  --gm-gold-metal:conic-gradient(from 210deg at 50% 50%,#f7e39a 0deg,#d4af37 55deg,#9a7418 120deg,#e6c65a 180deg,#f7e39a 220deg,#b8922a 290deg,#f7e39a 360deg);
  --gm-disc-fill:radial-gradient(circle at 50% 40%,#161616 0,#080808 60%,#000 100%);
}
</style>
