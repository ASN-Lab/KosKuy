# KosKuy - Backend (Laravel Monolith)

Platform marketplace kos & barang bekas mahasiswa.

## Tech Stack
- Laravel 11 (PHP 8.4+)
- FilamentPHP v5 (Admin Panel)
- MySQL 8.0 (database: `koskuy_db`)
- Tailwind CSS + Blade (Web Views)
- Laravel Sanctum (Mobile API Auth)

## Setup Lokal
1. Clone repo, jalankan `composer install`
2. Copy `.env.example` → `.env`, isi konfigurasi MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=koskuy_db
   DB_USERNAME=root
   DB_PASSWORD=****
   ```
3. Jalankan perintah:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```
4. Akses http://127.0.0.1:8000

## Akses
- **Web publik:** /
- **Admin panel:** /admin
- **API base:** /api/v1

## Struktur
- `app/Filament/Resources`: Admin panel CRUD
- `app/Http/Controllers/Api`: REST API v1 (Flutter)
- `app/Http/Controllers`: Web controllers
- `app/Models`: Eloquent models
- `database/migrations`: Skema database
- `database/seeders`: Data dummy
- `resources/views`: Blade templates

## API Endpoints (Read-Only)
- `GET /api/v1/kos`: list kos + filter
- `GET /api/v1/kos/{id}`: detail kos
- `GET /api/v1/thrift`: list barang bekas
- `GET /api/v1/thrift/{id}`: detail barang
- `GET /api/v1/categories`: list kategori

## Kontribusi
1. **Branching:** Gunakan branch `feature/nama-fitur` untuk pengembangan baru.
2. **Pull Request:** Kirim PR ke branch `main`. Pastikan fitur berjalan tanpa error sebelum melakukan merge.
3. **Standar:** Ikuti PSR-12 untuk style coding dan berikan comment jika ada logic kompleks.

## Tim
- Backend Repository: github.com/ASN-Lab/KosKuy
- Mobile Repository: github.com/ASN-Lab/koskuy_mobile
