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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('plate_number', 20)->unique();
            $table->string('model', 100);
            $table->string('vehicle_type', 100); // van, truck, car, SUV
            $table->year('year');
            $table->string('color', 50)->nullable();
            $table->unsignedInteger('current_odometer_km')->default(0);
            $table->string('photo', 255)->nullable(); // file path
            $table->enum('status', ['active', 'in_shop', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
