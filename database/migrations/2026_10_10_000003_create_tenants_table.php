<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kos_listing_id')->constrained('kos_listings')->cascadeOnDelete();
            $table->foreignId('kos_room_id')->nullable()->constrained('kos_rooms')->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->decimal('monthly_price', 12, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'checked_out'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
