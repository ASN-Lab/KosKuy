# AGENTS.md

Guidance for AI agents and new contributors working on KosKuy.

## What this is

A student housing + thrift marketplace for Indonesian campuses. Public storefront,
partner APIs, an auth-gated dashboard, a newsletter endpoint, and a printable
promo flyer — all on Laravel with a Tailwind/Alpine frontend.

## Stack

- PHP 8.4, Laravel 11, Filament v5 (admin panel), Sanctum (API auth), SQLite
- Tailwind CSS v4 via `@tailwindcss/vite`, Alpine.js 3, Vite 8 (rolldown)
- PHPUnit 10

## Setup

```bash
composer install
npm install                 # use `npm.cmd` on PowerShell, see Gotchas
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

`database/database.sqlite` is seeded with demo listings so the storefront is
never empty. Do not delete it.

## Commands

| Command | What it does |
| --- | --- |
| `composer qa` | **Run this before handing off.** Full gate below. |
| `composer test` / `php artisan test` | PHPUnit feature tests |
| `composer qa:smoke` | 43 route/API/auth/newsletter checks over the real middleware stack |
| `composer qa:audit` | Rendered-HTML audit (guest). Add `"" auth` for the dashboard |
| `composer qa:links` | Verifies social/contact/QR links render from config, not hardcoded |
| `composer qa:copy` | Scans view copy for mojibake / stray characters |
| `composer qa:icons` | Every `<x-ui.icon>` name resolves to a bundled icon |
| `npm run build` | Production assets → `public/build/` |
| `npm run qr` | Regenerate `public/images/newsletter-qr.png` from `KOSKUY_URL` |
| `npm run qr:verify` | Print the QR matrix as ASCII and validate its structure |
| `php scripts/render-one.php <uri>` | Dump one rendered page, for debugging markup |

## Conventions

- **Icons**: always `<x-ui.icon name="heroicons-outline-xxx" />`. Add to
  `app/Support/Icons.php` by running `php scripts/gen-icons.php`, never by hand.
- **Brand/contact config**: read everything from `config('koskuy.*')`. Never
  hardcode a domain, social handle, phone number, or address in a view. `qa:links`
  enforces this.
- **Socials** are filtered before rendering, so an unset value simply omits the
  icon instead of emitting a dead link. Keep that behaviour.
- **Flyer** shows the configured host, not `APP_URL`. It must degrade gracefully
  when `KOSKUY_NEWSLETTER_QR` is unset.
- Copy is Indonesian, sentence case, no exclamation-mark stacking.

## Gotchas

These cost real time to rediscover. Please don't undo them.

1. **`npm.cmd`, not `npm`.** PowerShell's execution policy blocks `npm.ps1`.
2. **Don't bind a port.** `php artisan serve` and `npm run dev` fail in this
   sandbox ("socket fail"). The `scripts/*.php` harnesses dispatch through the
   HTTP kernel directly instead — that's deliberate, not a workaround to remove.
3. **Test DB isolation is load-bearing.** `phpunit.xml` and `tests/TestCase.php`
   force `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` *before* the app boots.
   `RefreshDatabase` on the file DB will wipe `database/database.sqlite`. Keep
   that env forcing; it has already saved the demo data once.
4. **Carry the session cookie.** A direct-kernel harness that POSTs must reuse
   the session cookie from the GET that produced the CSRF token, and Laravel's
   session store is a singleton across requests within one process.
5. **`cache()` is `remember()`.** Writing cache with the global helper
   `cache($key, $value, $ttl)` silently does the wrong thing. Use `Cache::put()`.
6. **Vite manifest path.** `vite.config.js` sets `manifest: 'manifest.json'` so
   it lands at `public/build/manifest.json`, which is where `@vite` looks.
   Don't let it move to `build/.vite/manifest.json`.
7. **`scripts/*.php` live outside `storage/app`** on purpose: `storage/app/.gitignore`
   is `*`, so tooling parked there never gets committed.
8. **Demo values live in `.env`, not in config defaults.** `config/koskuy.php`
   and `.env.example` intentionally ship empty contact/social values so a fresh
   deploy renders no fake links. Real values go in the deployment `.env`.
9. After changing `KOSKUY_URL`, re-run `npm run qr` and re-check `/flyer`.

## Before you finish

Run `composer qa` and `npm run build`. Both must be clean, and
`npm audit` must report 0 vulnerabilities.
