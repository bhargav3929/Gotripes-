# Booking Notification Emails

When a customer books anything on Gotripes, the business owner / staff should get an
email telling them an order came in. This doc tracks that feature across every bookable
product and explains how it works so any new feature follows the same recipe.

- **Recipients are per-entity** — you set the notify-list on the thing being booked
  (each activity, each package), or per-service for products with no catalog row
  (eSIM, visa). Empty list ⇒ falls back to the company email so nothing is ever lost.
- **Sending is free** via the Hostinger domain mailbox `bookings@gotrips.ai`
  (`smtp.hostinger.com`). No paid tool. Volume is tiny (~20–30/day).

---

## Status

| Feature           | Recipients field            | Where configured              | Send wired | Verified |
|-------------------|-----------------------------|-------------------------------|------------|----------|
| **Activities**    | ✅ per-activity column      | each activity's create/edit (admin/mgr/agent) | ✅ | ✅ feature test |
| **FIFA tickets**  | ✅ per-service              | Manager → Booking Notifications | ✅       | ✅ feature test |
| **e-Visa (Fluxir)**| ✅ per-service             | Manager → Booking Notifications | ✅       | ✅ feature test (credit flow + dedupe) |
| **eSIM (Monty)**  | ✅ per-service              | Manager → Booking Notifications | ✅ (Nomod paid callback) | ✅ recipient-resolution test¹ |
| **Tour packages** | ✅ per-package column       | each package's create/edit (admin/mgr/agent) | ✅ on-site enquiry form | ✅ feature test |
| Umrah packages    | — n/a                       | —                             | — payment flow captures no customer email | — |

Legend: ✅ done · — not applicable.

¹ eSIM fires inside the Nomod payment callback, which then calls the external MontyeSIM
API — so the automated test asserts the recipient resolution + Mailable (not the live
webhook). The send line is identical to the e-Visa path, which is covered end-to-end.

**Tour packages** previously had no customer action to notify on — so an on-site
**enquiry form** was added to the package page (`PackageEnquiryController`, route
`tour-packages.enquire`, stored in `package_enquiries`). Submitting it emails the
package's per-package recipients.

**Still no hook** (nothing to send yet):
- **Umrah** payments capture only `package_name` + `amount` (no customer email is stored).
Needs a customer booking/enquiry flow built first; once it exists, follow the recipe below.

A row is **Verified** when its automated test passes. For production, also do one real
send to `bookings@gotrips.ai` after the Hostinger `.env` is set (see below).

---

## How recipients resolve

All in `app/Helpers/notifications.php` (loaded via `TenantServiceProvider` — no
`composer dump-autoload` needed on FTP deploys):

- `parse_emails($raw)` — split a comma/newline/semicolon list → unique, valid, lowercased.
- `service_notification_emails($service, $company = null)` — read a tenant's per-service
  list from `company.settings → booking_notification_emails.{service}`. Pass an explicit
  `$company` in webhook contexts (eSIM) so it reads the **order's** tenant, not the apex host.
- `booking_recipients($entityEmails, $customerEmail = null)`:
  1. Use the configured emails (per-activity column, or per-service setting).
  2. If none, fall back to `current_company()->email`, else `config('mail.from.address')`.
  3. Append the customer's email when passed (activities pass it; service notifications don't,
     since the customer already gets their own QR/visa/confirmation).
  4. Return a unique, validated, lowercased list.

Two recipient stores:
- **Activities** → `notification_emails` TEXT column on `tbl_UAEActivities`, edited per item.
- **eSIM / Visa / FIFA** → `company.settings.booking_notification_emails[service]`, edited at
  **Manager → Settings → Booking Notifications** (`Manager\SettingsController::notifications`).

Validation everywhere uses `App\Rules\EmailList` (≤10 addresses, each valid). Service
notifications use the generic `App\Mail\BookingNotificationMail` +
`resources/views/emails/booking_notification.blade.php`.

---

## Hostinger SMTP setup (one-time, production `.env`)

1. hPanel → Emails → create mailbox `bookings@gotrips.ai` (free with hosting).
2. Set in **production `.env`** (via hPanel File Manager — `.env` is not in git):

   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=465
   MAIL_ENCRYPTION=ssl
   MAIL_USERNAME=bookings@gotrips.ai
   MAIL_PASSWORD=<mailbox password>
   MAIL_FROM_ADDRESS=bookings@gotrips.ai
   MAIL_FROM_NAME="GoTrips"
   ```

3. Run `php artisan config:clear` on prod (via `public/gt-deploy-run.php`).
4. Local dev: keep `MAIL_MAILER=log` so test bookings write to
   `storage/logs/laravel.log` instead of sending real mail.

---

## How to add this to a new feature

1. **Catalog item** (has an admin-created row, like activities/packages):
   - migration: add `notification_emails` TEXT nullable to the table;
   - model: add to `$fillable` + a `getNotificationEmailListAttribute()` accessor
     returning `parse_emails($this->notification_emails)`;
   - controllers (every surface — admin/manager/agent): validate with
     `['nullable', new \App\Rules\EmailList]` and persist `$request->notification_emails`;
   - create + edit blade forms: add the textarea (copy the activities field);
   - booking handler: `booking_recipients($item->notification_email_list, $customerEmail)`.
2. **Service product** (no catalog row — eSIM/visa/FIFA): store one list per company in
   a `service_notification_settings` row (`company_id`, `service_key`, `emails`), edited
   from that service's settings screen; resolve with the same `booking_recipients()`.

---

## Verification (per feature, end-to-end)

1. `php artisan migrate`; confirm the column/table exists.
2. With `MAIL_MAILER=log`: create the item in each management surface, enter 3 emails,
   confirm the row saves them and the edit form repopulates them.
3. Place a test booking; inspect `storage/logs/laravel.log` — the rendered email must
   list exactly the configured recipients + customer email, correct `from`/`reply-to`.
4. Test the empty-field case → falls back to the company email.
5. Once over real Hostinger SMTP, confirm an email lands in `bookings@gotrips.ai`.
6. Tick the row's **Verified** box above.

Deploy preview-first: show on local (`127.0.0.1:8000`) first; deploy to prod
(FTP + `gt-deploy-run.php` for `migrate` / `config:clear`) only after approval.
