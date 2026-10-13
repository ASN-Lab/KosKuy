# AGENTS.md

Guidance for AI agents and new contributors working on KosKuy.

## What this is

A student housing + thrift marketplace for Indonesian campuses. Public storefront,
partner APIs, an auth-gated dashboard, and a newsletter endpoint — all on Laravel
with a Tailwind/Alpine frontend.

## Stack

- PHP 8.4, Laravel 11, Filament v5 (admin panel), Sanctum (API auth)
- Database: SQLite by default; PostgreSQL (incl. Supabase) supported via the
  `pgsql` driver — see Gotcha 11
- Tailwind CSS v4 via `@tailwindcss/vite`, Alpine.js 3, Vite 8 (rolldown)
- PHPUnit 10

## Roles & access

Three roles, three separate areas — keep them that way:

| Role | Landing | Can reach |
| --- | --- | --- |
| `student` | `/dashboard` → student dashboard | Storefront + recommendations only. No management, no panel links. |
| `owner` | `/dashboard` → owner dashboard | Own kos listings + the Filament panel (`/admin`), scoped to their rows. |
| `admin` | `/dashboard` → redirects to `/admin` | Full Filament panel, including `UserResource` and `CategoryResource` (admin-only via `canAccess()`). |

- `AuthController@dashboard` dispatches by role; the student/owner dashboards
  live in `resources/views/dashboard/`. Don't reintroduce a shared dashboard view.
- The footer "Panel Pengelola" link only renders for `admin`/`owner`.
- Registration offers `student` and `owner` only; `admin` accounts are seeded.
- **Thrift has no Filament resource.** It is a standalone business line with its
  own future flow, so its admin panel option was removed deliberately — only the
  public storefront (`/thrift`, `ThriftController`, the API) and seed data remain.
  Don't reintroduce `App\Filament\Resources\ThriftProducts`.
- Owner rental management lives in three Filament resources under the
  "Pengelolaan Kos" group — `KosRooms`, `Tenants`, `Payments` — each scoped via
  `getEloquentQuery()`/`modifyQueryUsing` to the owner's `owner_id`. The owner
  dashboard (`dashboard/owner.blade.php`) renders occupancy/cash from
  `AuthController@rentalOverview`, driven by `KosRoom`/`Tenant`/`Payment`
  (`kos_rooms`, `tenants`, `payments`, plus profile columns on `users`).
- Account settings (`/pengaturan`, `SettingsController`) are available to every
  role and include avatar, password, notification prefs, and account deletion.
- **The navbar is different once signed in.** Guests see the public browse links
  (Beranda / Cari Kos / Thrift, marked `data-public-nav`), the Cari Kos CTA and
  the mobile menu. Signed-in users keep none of those — the navbar shows only
  the brand and the account chip, both pointing at their dashboard. Students get
  a Kos/Thrift search hero embedded in `dashboard/student.blade.php` (both
  indexes take a `search` param). Keep the guest-only nav wrapped in `@guest`.

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
| `composer qa:links` | Verifies social/contact links render from config, not hardcoded |
| `composer qa:copy` | Scans view copy for mojibake / stray characters |
| `composer qa:icons` | Every `<x-ui.icon>` name resolves to a bundled icon |
| `npm run build` | Production assets → `public/build/` |
| `php scripts/fetch-demo-photos.php` | Re-download the demo photography into `storage/app/public` |
| `php scripts/warm-cache.php` | Pre-populate the storefront cache right before a demo |
| `php scripts/render-one.php <uri>` | Dump one rendered page, for debugging markup |

## Conventions

- **Icons**: always `<x-ui.icon name="heroicons-outline-xxx" />`. Add to
  `app/Support/Icons.php` by running `php scripts/gen-icons.php`, never by hand.
- **Brand/contact config**: read everything from `config('koskuy.*')`. Never
  hardcode a domain, social handle, phone number, or address in a view. `qa:links`
  enforces this.
- **Socials** are filtered before rendering, so an unset value simply omits the
  icon instead of emitting a dead link. Keep that behaviour.
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
9. **`public/storage` must be a junction, not a folder.** An empty leftover
   `public/storage` directory makes every listing photo 404 while `artisan
   storage:link` claims the link already exists. Delete the folder, then re-run
   `php artisan storage:link`.
10. **Listing photos are keyed by row id.** `storage/app/public/kos/kos-{id}-{n}.jpg`
    and `thrift/thrift-{id}.jpg` must match the seeded ids. Re-run
    `php scripts/fetch-demo-photos.php` after adding demo rows.
11. **Supabase is PostgreSQL, not MySQL.** Use `DB_CONNECTION=pgsql` and the
    Session pooler host (`db.<ref>.supabase.co:5432`) — the Direct connection is
    IPv6-only on most plans. Enable `pdo_pgsql`/`pgsql` in `php.ini` and set
    `DB_SSLMODE=require`; `config/database.php` reads it via `env('DB_SSLMODE')`.
    Photos are local files, not stored in the DB — a fresh machine still needs
    `php artisan storage:link` + `php scripts/fetch-demo-photos.php`.
12. **Filament resource access is `canAccess()`.** Restrict a resource to a role
    by overriding `public static function canAccess(): bool` — it also hides the
    nav item. Do not gate via `shouldRegisterNavigation()`, which leaves the
    route reachable.
13. **`<x-ui.icon>` forwards extra attributes.** The component passes
    `$attributes` into `Icons::svg()`, so `x-show`/`x-cloak`/`::class` on an
    icon actually reach the `<svg>`. It did *not* for a long time, which made
    both the hamburger and the close icon render at once. Keep that forwarding
    (one Alpine directive on an icon, no `<span class="contents">` workaround).
14. **The demo DB is remote (Supabase, `ap-northeast-1`) — reads cost ~300ms.**
    The local `.env` uses `SESSION_DRIVER=file` + `CACHE_STORE=file` (not
    `database`) so a request doesn't pay for session/cache round-trips. Storefront
    reads go through `App\Support\StorefrontCache` (version-keyed, long TTL),
    invalidated by the model observers registered in `AppServiceProvider`. Run
    `php scripts/warm-cache.php` right before a demo. Never put a DB query
    directly in a storefront Blade view — the show pages' owner/seller counts are
    computed in the controller for exactly this reason (`kos/show` and
    `thrift/show` used to count in the view on every render).
15. **The offline screen is one partial in both web layouts.** `partials/offline`
    is included by `layouts/app` and `layouts/guest` and driven by the
    `offlineScreen` Alpine component in `resources/js/app.js`. It reacts to the
    browser's `online`/`offline` events *and* to a `network-error` event that
    `app.js` dispatches when `fetch`/`axios` fail with no response — so wrapping
    requests differently shouldn't silently bypass it. It confirms connectivity
    with a `HEAD` ping before hiding. The Filament panel does **not** load
    `app.js`, so it has its own self-contained copy: `filament/offline-script`
    (registered on `HEAD_END`, re-declares `offlineScreen` before Alpine boots)
    and `filament/offline` (registered on `BODY_END`, scoped `kk-offline*` CSS +
    `Icons::svg`). Both are wired in `AdminPanelProvider`. Keep those two in step
    with the storefront partial if the behaviour changes.

16. **Branded error pages + the 419/logout trap.** `resources/views/errors/`
    ships a `layout` plus `403/404/419/500/503` (dark storefront styling,
    `@include('partials.offline')`). Separately, `bootstrap/app.php` turns a
    stale-token **logout POST** into a redirect home instead of a 419 wall.
    Careful: `Handler::prepareException()` rewrites
    `Illuminate\Session\TokenMismatchException` into a Symfony
    `HttpException(419)` *before* `render()` callbacks run, so a callback typed
    on `TokenMismatchException` never matches — type on `HttpException` and
    filter on `getStatusCode() === 419` (guarded by `routeIs('logout') || is('logout')`).
    `tests/Feature/ErrorPagesTest.php` locks both behaviours in.

## Before you finish

Run `composer qa` and `npm run build`. Both must be clean, and
`npm audit` must report 0 vulnerabilities.
