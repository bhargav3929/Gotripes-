/*
 * Storefront behaviour. Runs after the page has loaded (deferred).
 *
 * window.GoTripsTheme tells you where you are:
 *   GoTripsTheme.tenant   your company's id, e.g. "savanna"
 *   GoTripsTheme.surface  "storefront" here, "dashboard" in dashboard.js
 *   GoTripsTheme.page     the page, e.g. "emirates.index" (see README.md)
 *   GoTripsTheme.asset("assets/hero.jpg")  full URL of a file in this repo
 *
 * Theme scripts do not run on sign-in, password or payment pages, and not
 * while a GoTrips administrator is viewing your site.
 */
(function () {
  var theme = window.GoTripsTheme;
  if (!theme) return;

  // Example: add a class you can style on one page only.
  if (theme.page === '/') {
    document.body.classList.add('my-brand-home');
  }
})();
