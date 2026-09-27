<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabase extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Cek koneksi database';

    public function handle()
    {
        try {
            DB::connection()->getPdo();
            $this->info("✅ Koneksi database berhasil: " . DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            $this->error("❌ Gagal konek DB: " . $e->getMessage());
        }
    }
}
