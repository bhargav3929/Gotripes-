# GoTrips SaaS Admin Guide

## Access URLs

| Panel | URL | Description |
|-------|-----|-------------|
| Super Admin | `/superadmin` | Platform-wide management |
| Client Admin | `/client` | Company-specific dashboard |
| Public Login | `/admin` | Standard login page |

## Default Credentials

### Super Admin
- **Username:** `John Smith` (or any user with `is_super_admin=1` or `role=super_admin`)
- **Login Field:** Uses `name` field, NOT email

### Client Admin
After creating a company in Super Admin panel, use the admin credentials set during company creation.

## Features Overview

### Super Admin Panel (`/superadmin`)

1. **Dashboard** - Platform statistics, recent activity
2. **Companies** - Create/manage tenant companies
   - White-label branding (logo, colors)
   - Subscription management (plans, extend)
   - Impersonate company admins
3. **Users** - View/edit all platform users
4. **Reports** - Revenue by company, orders by status, monthly trends
5. **Settings** - Platform name, currency, SMTP, trial duration

### Client Admin Panel (`/client`)

1. **Dashboard** - Company statistics (eSIM, visa, bookings)
2. **Orders** - eSIM order management
3. **Visa** - Visa application tracking
4. **Activities** - Activity booking management
5. **Flights & Hotels** - Flight/hotel booking records
6. **Analytics** - Revenue trends, top countries
7. **Branding** - Company logo, colors, domain
8. **Settings** - Currency, timezone, markup percentage

## Multi-Tenant Architecture

- Each company has isolated data via `company_id` on all tables
- `BelongsToCompany` trait auto-filters queries by company
- `IdentifyTenant` middleware detects company from subdomain/domain
- Super admins bypass company filtering for platform-wide access

## Quick Commands

### SSH to Server
```bash
ssh -p 65002 u705168859@145.79.212.28
cd ~/domains/gotrips.ai/public_html
```

### Deploy Updates
```bash
git pull origin main
php artisan view:clear
php artisan cache:clear
```

### Extend Subscription (via tinker)
```bash
php artisan tinker
>>> $c = App\Models\Company::find(1);
>>> $c->subscription_ends_at = now()->addYear();
>>> $c->save();
```

## Theme

- Background: `#0a0a0a` to `#1a1a1a`
- Gold accent: `#FFD700`
- Dark cards: `rgba(255,255,255,0.03)`
- Border: `rgba(255,255,255,0.1)`
