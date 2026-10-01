{{--
  A plainly labelled way back to the GoTrips home page, for standalone pages
  that carry no site header (partner sign-in and register). Amer, 1 Oct 2026:
  "there is no link to go back to home page". The /freelancer landing page has
  its own header and carries the same pill inline.

  Fixed top-left so it is the first thing a visitor who landed on a dead end
  can find. Needs Font Awesome (all the pages that include it already load it).
--}}
<a class="gt-home-pill" href="{{ url('/') }}"><i class="fas fa-house"></i> Home</a>
<style>
    .gt-home-pill {
        position: fixed; top: 18px; left: 20px; z-index: 60;
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px;
        border: 1px solid rgba(255, 215, 0, 0.35); border-radius: 99px;
        background: rgba(5, 5, 5, 0.85);
        -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px);
        color: #FFD700; text-decoration: none;
        font: 700 12px/1 'Plus Jakarta Sans', sans-serif;
        letter-spacing: 1.2px; text-transform: uppercase;
        transition: background 0.2s, border-color 0.2s, transform 0.2s;
    }
    .gt-home-pill:hover { background: rgba(255, 215, 0, 0.12); border-color: #FFD700; color: #FFD700; transform: translateY(-1px); }
    .gt-home-pill:focus-visible { outline: 2px solid #FFD700; outline-offset: 3px; }
    @media (max-width: 900px) { .gt-home-pill { top: 12px; left: 12px; } }
</style>
