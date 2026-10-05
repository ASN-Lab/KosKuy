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
            $table->foreignId('kos_id')->constrained('kos_listings')->cascadeOnDelete();
            $table->string('room_name');
            $table->boolean('is_occupied')->default(false);
            $table->string('tenant_name')->nullable();
            $table->timestamps();
        });

        Schema::table('kos_facilities', function (Blueprint $table) {
            $table->enum('condition_status', ['normal', 'under_repair'])->default('normal')->after('facility_name');
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete()->after('condition_status');
        });

        Schema::create('kos_condition_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kos_id')->constrained('kos_listings')->cascadeOnDelete();
            $table->morphs('subject');
            $table->enum('status', ['normal', 'under_repair'])->default('normal');
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kos_condition_updates');

        Schema::table('kos_facilities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_updated_by');
            $table->dropColumn('condition_status');
        });

        Schema::dropIfExists('kos_rooms');
    }
};
