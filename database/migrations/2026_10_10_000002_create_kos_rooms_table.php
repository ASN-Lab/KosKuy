<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kos_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kos_listing_id')->constrained('kos_listings')->cascadeOnDelete();
            $table->string('room_number');
            $table->decimal('price_per_month', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['kos_listing_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kos_rooms');
    }
};
