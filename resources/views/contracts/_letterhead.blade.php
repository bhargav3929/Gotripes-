{{--
  Shared GoTrips letterhead for contract PDFs — Amer asked for this on the
  eSIM Sub-Agent Agreement, 27 Sep 2026 (docs/client-feedback-2026-09-27.md,
  D5): "I will need your hands on for letter head to be embedded on this
  contract to look more professional." Applied here so every auto-generated
  signature record (signed-agreement, signature-certificate) carries it too,
  not just the one document.

  dompdf repeats any `position: fixed` element on every page, which is how
  the header/footer bands below appear on page 2+ without extra markup.
  White background deliberately — this prints and gets emailed to outside
  parties, so it follows normal business-letter conventions rather than the
  site's dark gold-and-black theme.
--}}
<style>
    /* Explicit white on @page itself, not just html/body — dompdf's page
       canvas (including the margin band) is otherwise left transparent, which
       most viewers paper over with a white canvas but is a real problem
       anywhere the transparency is preserved, e.g. exporting a page as an
       image, or a viewer with a dark background. */
    @page { margin: 130px 50px 90px 50px; background: #ffffff; }
    html, body { font-family: DejaVu Sans, sans-serif; color: #1a1a1a; background: #ffffff; }
    .gt-letterhead-header {
        position: fixed; top: -110px; left: 0px; right: 0px; height: 90px;
        border-bottom: 2px solid #d4af37;
        padding-bottom: 10px;
    }
    .gt-letterhead-header table { width: 100%; border-collapse: collapse; }
    .gt-letterhead-header td { vertical-align: middle; }
    .gt-letterhead-logo { width: 52px; }
    .gt-letterhead-logo img { width: 44px; height: 44px; }
    .gt-letterhead-brand { padding-left: 12px; }
    .gt-letterhead-brand .name { font-size: 20px; font-weight: bold; color: #1a1a1a; }
    .gt-letterhead-brand .tagline { font-size: 10px; color: #777; margin-top: 2px; }
    .gt-letterhead-doctype { text-align: right; font-size: 11px; color: #8f6a16; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    .gt-letterhead-footer {
        position: fixed; bottom: -70px; left: 0px; right: 0px; height: 50px;
        border-top: 1px solid #ddd; padding-top: 8px;
        font-size: 9px; color: #999;
    }
    .gt-letterhead-footer table { width: 100%; }
    .gt-letterhead-footer .right { text-align: right; }
</style>

<div class="gt-letterhead-header">
    <table>
        <tr>
            <td class="gt-letterhead-logo"><img src="{{ public_path('assets/index_files/logo.png') }}"></td>
            <td class="gt-letterhead-brand">
                <div class="name">GoTrips</div>
                <div class="tagline">Travel &amp; Connectivity Solutions &middot; Dubai, UAE</div>
            </td>
            <td class="gt-letterhead-doctype">{{ $letterheadDocType ?? 'Sub-Agent Agreement' }}</td>
        </tr>
    </table>
</div>

<div class="gt-letterhead-footer">
    <table>
        <tr>
            <td>GoTrips &middot; admin@gotrips.ai &middot; gotrips.ai</td>
            <td class="right">Confidential &mdash; not for distribution outside the signing parties</td>
        </tr>
    </table>
</div>
