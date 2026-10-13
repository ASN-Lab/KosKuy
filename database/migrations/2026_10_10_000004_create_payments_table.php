<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('kos_listing_id')->constrained('kos_listings')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('period', 7);
            $table->enum('status', ['paid', 'pending'])->default('paid');
            $table->date('paid_at')->nullable();
            $table->string('method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['kos_listing_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
