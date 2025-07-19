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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('license_plate')->unique();
            $table->string('color')->nullable();
            $table->integer('total_seats')->default(0);
            $table->integer('available_seats')->default(0);
            $table->enum('status', ['available', 'reserved', 'full', 'inactive'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
