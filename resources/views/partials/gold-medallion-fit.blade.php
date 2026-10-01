{{--
  Keeps the circular pop-ups circular on short browser windows.

  Why this exists (1 Oct 2026): the registration pop-ups only drew the circle
  when the window was at least 770px tall and fell back to a rounded box
  below that. A typical Windows laptop has a browser window about 600-650px
  tall, so the founder (and most visitors on such laptops) never saw the circle
  Amer asked for. A form cannot be redrawn smaller than its own inputs, so
  instead of switching shape the whole disc is scaled to fit the window.

  How it works: every shell that carries data-gm-fit="<min disc px>" gets a
  --gm-fit custom property between 0 and 1, and its CSS applies it with the
  independent `scale` property (so it composes with the entry animation's
  `transform`). The disc itself is never drawn smaller than its minimum (the
  size that still contains the form), so text stays at a readable size; only
  windows too short for the minimum get scaled down.

  The formula mirrors the CSS exactly:
      --disc: max(MIN, min(920px, 96vw, 92vh))
  Below ~536px of height the scale would drop under ~0.72 and text becomes
  hard to read, so the CSS switches to the rounded-card fallback there (and on
  phones and tablets narrower than 900px, where a circle cannot hold a form).

  Include once per page that has a circular pop-up; @once keeps it to one copy
  when several partials ask for it.
--}}
@once
<script>
(function () {
    var MAX_DISC = 920, DEFAULT_MIN = 720, MARGIN = 24;

    function fit() {
        var w = window.innerWidth, h = window.innerHeight;
        var shells = document.querySelectorAll('[data-gm-fit]');
        for (var i = 0; i < shells.length; i++) {
            var min = parseFloat(shells[i].getAttribute('data-gm-fit')) || DEFAULT_MIN;
            var disc = Math.max(min, Math.min(MAX_DISC, w * 0.96, h * 0.92));
            var scale = Math.min(1, (w - MARGIN) / disc, (h - MARGIN) / disc);
            shells[i].style.setProperty('--gm-fit', scale.toFixed(4));
        }
    }

    fit();
    document.addEventListener('DOMContentLoaded', fit);
    window.addEventListener('resize', fit);
    window.addEventListener('orientationchange', fit);
})();
</script>
@endonce
