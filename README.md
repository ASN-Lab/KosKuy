# KosKuy - Backend (Laravel Monolith)

Platform marketplace kos & barang bekas (thrift) mahasiswa. Fitur utama: katalog kos & thrift, booking kos, jual-beli barang bekas, chat realtime (Student ↔ Owner/Seller), pembayaran manual (Transfer Bank / COD), serta dashboard monitoring kos untuk owner.

## Tech Stack
- Laravel 11 (PHP 8.4+)
- FilamentPHP v5 (Admin Panel)
- MySQL 8.0 (database: `koskuy_db`)
- Tailwind CSS + Blade + Alpine.js 3 (Web Views)
- Laravel Sanctum (Mobile/API Auth)
- Laravel Reverb (WebSocket Realtime) + Queue Database

## Setup Lokal
1. Clone repo, jalankan:
   ```bash
   composer install
   npm install
   ```
2. Copy `.env.example` → `.env`, isi konfigurasi MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=koskuy_db
   DB_USERNAME=root
   DB_PASSWORD=****
   ```
3. Set konfigurasi broadcast & queue:
   ```env
   BROADCAST_CONNECTION=reverb
   QUEUE_CONNECTION=database
   REVERB_PORT=8081
   REVERB_SERVER_PORT=8081
   VITE_REVERB_PORT=8081
   ```
4. Jalankan perintah:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   php artisan serve
   ```
5. Proses latar belakang untuk realtime chat (jalankan di terminal terpisah):
   ```bash
   php artisan reverb:start
   php artisan queue:work
   ```
6. Akses http://127.0.0.1:8000

## Akses
- **Web publik:** `/`
- **Admin panel:** `/admin` (login `admin@koskuy.id` / `admin123`)
- **Dashboard Owner:** `/owner/dashboard`
- **Dashboard Seller:** `/seller/dashboard`
- **API base:** `/api/v1`

## Akun Demo (Seeder)
| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@koskuy.id` | `admin123` |
| Owner | `owner@koskuy.id` | `owner123` |
| Seller | `seller@koskuy.id` | `seller123` |
| Student | `mahasiswa@koskuy.id` | `mahasiswa123` |

## Struktur
- `app/Filament/Resources`: Admin panel CRUD (termasuk Payment)
- `app/Http/Controllers/Api/V1`: REST API v1 (Flutter) — kos, thrift, booking, chat, payment, monitoring
- `app/Http/Controllers/Web`: Web controllers — auth, owner, seller, student, messages, checkout
- `app/Events`: Event broadcast realtime (`MessageSent`)
- `app/Models`: Eloquent models (KosListing, ThriftProduct, Booking, Payment, Conversation, Message, dll)
- `app/Services`: Business logic (`PaymentService`)
- `database/migrations`: Skema database
- `database/seeders`: Data dummy + akun demo
- `resources/views`: Blade templates
- `resources/js/echo.js`: Client Reverb (Echo)
- `routes/channels.php`: Private channel authorization

## API Endpoints (Read-Only / Public)
- `GET /api/v1/kos`: list kos + filter
- `GET /api/v1/kos/{id}`: detail kos
- `GET /api/v1/thrift`: list barang bekas
- `GET /api/v1/thrift/{id}`: detail barang
- `GET /api/v1/categories`: list kategori

## API Endpoints (Auth: Bearer Token Sanctum)
- `POST /api/v1/login` · `POST /api/v1/register` · `POST /api/v1/logout`
- `GET /api/v1/me` · `PUT /api/v1/me`
- `POST /api/v1/bookings` · `GET /api/v1/my-bookings`
- `POST /api/v1/thrift/{id}/purchase` · `GET /api/v1/my-purchases`
- `POST /api/v1/payments` · `GET /api/v1/payments/{type}/{id}` · `POST /api/v1/payments/{id}/proof`
- `GET /api/v1/conversations` · `POST /api/v1/conversations` · `POST /api/v1/conversations/{id}/messages`
- `GET /api/v1/notifications` · `POST /api/v1/notifications/{id}/read`
- `GET /api/v1/owner/kos/{id}/monitoring` · `POST /api/v1/owner/kos/{id}/rooms`

## Fitur Pembayaran (Manual)
- **Transfer Bank:** pembeli mendapat nomor rekening owner/seller + kode unik; bukti transfer diunggah → diverifikasi admin.
- **COD (cash on pickup):** bayar tunai saat pengambilan barang; tanpa upload bukti.
- Status pembayaran: `unpaid` → `pending_verification`/`pending_pickup` → `paid`/`rejected`.

## Fitur Chat Realtime
- Percakapan antara Student ↔ Owner (kos) dan Student ↔ Seller (thrift).
- Web view memakai session auth (route `web`), API memakai Sanctum.
- Event broadcast via Reverb port `8081`; butuh `php artisan reverb:start` dan `php artisan queue:work`.

## Kontribusi
1. **Branching:** Gunakan branch `feature/nama-fitur` untuk pengembangan baru.
2. **Pull Request:** Kirim PR ke branch `main`. Pastikan fitur berjalan tanpa error sebelum melakukan merge.
3. **Standar:** Ikuti PSR-12 untuk style coding dan berikan comment jika ada logic kompleks.
4. **QA:** Jalankan `composer qa` (test, smoke, audit, links, copy, icons) dan `npm run build` sebelum hand-off.

## Tim
- Backend Repository: github.com/ASN-Lab/KosKuy
- Mobile Repository: github.com/ASN-Lab/koskuy_mobile
