<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('maintenance_type_id')->constrained('maintenance_types');
            $table->date('last_service_date')->nullable();
            $table->unsignedInteger('last_service_odo')->default(0);
            $table->unsignedInteger('interval_km'); // copied from maintenance_types.default_interval_km, overrideable
            $table->unsignedInteger('next_due_odo');  // computed: last_service_odo + interval_km
            $table->text('notes')->nullable();
            $table->timestamps();

            // A vehicle should only have one schedule per maintenance type
            $table->unique(['vehicle_id', 'maintenance_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
