{{--
  Medallion treatment for the full-page registration forms.

  Client request, 15 Sep 2026: "If we do same for customer registration pop
  window also as circular for all registrations forms."

  The pop-up is a literal disc because it shows one wizard step at a time. These
  pages carry dense multi-column forms, where a true circle leaves no usable
  width — so they take the same language instead: the brushed-gold ring at the
  same thickness ratio, the black radial panel, the inner hairline and the
  circular brand medallion on top. Identical vocabulary, shape that still works.

  Usage: @include('partials.gold-medallion-page') once in <head> or at the top
  of <body>, then add class="gm-ring-panel" to the form card and drop
  <div class="gm-brand"><img src="..." alt=""></div> above it.
--}}
@include('partials.gold-medallion-tokens')
<style>
.gm-brand{
  width:104px;height:104px;margin:0 auto 22px;border-radius:50%;
  overflow:hidden;background:#050505;
  box-shadow:0 10px 26px rgba(0,0,0,.6),0 0 0 1px rgba(212,175,55,.25);
}
.gm-brand img{display:block;width:100%;height:100%;object-fit:cover}

/* Same ring ratio as the disc, expressed against the panel's width so a wide
   form gets the same visual weight of gold without becoming a circle. */
.gm-ring-panel{
  position:relative;
  border:14px solid transparent !important;
  border-radius:34px !important;
  background:var(--gm-disc-fill) padding-box,var(--gm-gold-metal) border-box !important;
  box-shadow:0 30px 90px rgba(0,0,0,.85),0 0 60px rgba(212,175,55,.16),
             inset 0 0 0 1px rgba(60,42,8,.9) !important;
}
.gm-ring-panel::before{
  content:'';position:absolute;inset:10px;border:2px solid rgba(212,175,55,.55);
  border-radius:22px;pointer-events:none;z-index:0;
}
.gm-ring-panel > *{position:relative;z-index:1}

@media (max-width:640px){
  .gm-ring-panel{border-width:9px !important;border-radius:26px !important}
  .gm-ring-panel::before{inset:7px;border-radius:17px}
  .gm-brand{width:84px;height:84px;margin-bottom:16px}
}
</style>
