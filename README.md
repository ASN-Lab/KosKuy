# KosKuy

KosKuy is a student housing and thrift marketplace built with Laravel 11.

## Tech Stack
- **Backend**: Laravel 11, PHP 8.4, MySQL
- **Realtime**: Laravel Reverb (WebSockets), Event Queues (database)
- **Frontend**: Tailwind CSS (via Vite), Alpine.js 3
- **Admin**: Filament v5

## Setup
1. `composer install`
2. `npm install`
3. `cp .env.example .env`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `php artisan reverb:start` (on port 8081)
7. `php artisan queue:work`
8. `npm run dev`

## Recent Changes
- Integrated realtime chat between students and owner/sellers via Reverb.
- Implemented manual checkout for thrift products (Transfer Bank / COD).
- Added monitoring dashboard for kos owners.
- Enabled queue-based processing for events.
