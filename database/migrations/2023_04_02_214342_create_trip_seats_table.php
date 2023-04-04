<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservation_seats', function (Blueprint $table) {
            $table->id();

            $table->string('hash_id')->nullable();
            $table->index('hash_id');

            $table->foreignId('seat_id')
                ->constrained('seats')
                ->cascadeOnDelete();

            $table->foreignId('reservation_id')
                ->constrained('reservations')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_reservations');
    }
};
