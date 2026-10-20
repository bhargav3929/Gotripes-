{{-- Typography for rendered help markdown. Shared by help/index (README intro)
     and help/show (full guide). Scoped under .help-prose so it never leaks
     into the rest of the manager UI. --}}
<style>
.help-prose{color:#d6d8db;font-size:14.5px;line-height:1.75;overflow-wrap:anywhere}
.help-prose>*:first-child{margin-top:0}
.help-prose h1,.help-prose h2,.help-prose h3,.help-prose h4{color:#fff;font-weight:700;letter-spacing:-.015em;line-height:1.2;margin:40px 0 14px}
.help-prose h1{font-size:32px;margin-top:0}
.help-prose h2{font-size:22px;padding-bottom:8px;border-bottom:1px solid var(--wp-border-light)}
.help-prose h3{font-size:17px}
.help-prose h4{font-size:14px;text-transform:uppercase;letter-spacing:.6px;color:var(--wp-primary);margin-top:28px}
.help-prose p{margin:0 0 16px}
.help-prose a{color:var(--wp-primary);text-decoration:underline;text-decoration-color:rgba(255,215,0,.35);text-underline-offset:3px;transition:text-decoration-color .15s ease}
.help-prose a:hover{text-decoration-color:var(--wp-primary)}
.help-prose strong{color:#fff;font-weight:600}
.help-prose ul,.help-prose ol{margin:0 0 18px;padding-left:24px}
.help-prose li{margin-bottom:6px}
.help-prose li>p{margin-bottom:6px}
.help-prose li::marker{color:var(--wp-primary)}
.help-prose blockquote{margin:0 0 18px;padding:12px 18px;border-left:3px solid var(--wp-primary);background:rgba(255,215,0,.06);border-radius:0 6px 6px 0;color:#c9c9c9}
.help-prose blockquote p:last-child{margin-bottom:0}
.help-prose code{font-family:Menlo,Consolas,'Liberation Mono',monospace;font-size:12.5px;background:#1a1a1a;border:1px solid var(--wp-border-light);border-radius:4px;padding:1px 6px;color:#ffe680}
.help-prose pre{background:#141414;border:1px solid var(--wp-border-light);border-radius:8px;padding:14px 16px;margin:0 0 18px;overflow-x:auto}
.help-prose pre code{background:none;border:0;padding:0;color:#e6e6e6;font-size:12.5px;line-height:1.6}
.help-prose table{width:100%;border-collapse:collapse;margin:0 0 22px;font-size:13px;display:block;overflow-x:auto}
.help-prose thead th{text-align:left;color:var(--wp-primary);font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;padding:10px 12px;border-bottom:1px solid var(--wp-border);white-space:nowrap}
.help-prose tbody td{padding:10px 12px;border-bottom:1px solid var(--wp-border-light);vertical-align:top}
.help-prose tbody tr:nth-child(even) td{background:rgba(255,255,255,.025)}
.help-prose tbody tr:hover td{background:rgba(255,215,0,.05)}
.help-prose td:only-child,.help-prose th:only-child{width:100%}
.help-prose hr{border:0;border-top:1px solid var(--wp-border-light);margin:32px 0}
.help-prose img{max-width:100%;border-radius:8px;border:1px solid var(--wp-border-light)}
.help-prose input[type=checkbox]{margin-right:6px;accent-color:var(--wp-primary)}
.help-prose kbd{font-family:Menlo,Consolas,monospace;font-size:12px;background:#1a1a1a;border:1px solid #444;border-bottom-width:2px;border-radius:4px;padding:1px 6px}
.help-prose details{border:1px solid var(--wp-border-light);border-radius:6px;padding:10px 14px;margin:0 0 16px}
.help-prose summary{cursor:pointer;color:#fff;font-weight:600}
@media(max-width:640px){.help-prose{font-size:14px}.help-prose h1{font-size:26px}.help-prose h2{font-size:19px}}
</style>
