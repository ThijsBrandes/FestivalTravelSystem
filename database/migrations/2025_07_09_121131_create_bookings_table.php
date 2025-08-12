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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('festival_id')
                ->constrained('festivals')
                ->onDelete('cascade');
            $table->timestamp('booked_at')->useCurrent();
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');
            $table->decimal('total_price', 8, 2)->default(0.00);
            $table->integer('total_points')->default(0);
            $table->integer('ticket_quantity')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
