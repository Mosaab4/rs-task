<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->string('hash_id')->nullable();
            $table->index('hash_id');

            $table->string('name');

            $table->foreignId('bus_id')
                ->constrained('buses')
                ->cascadeOnDelete();

            $table->foreignId('from_id')
                ->constrained('cities')
                ->cascadeOnDelete();

            $table->foreignId('to_id')
                ->constrained('cities')
                ->cascadeOnDelete();

            //$table->boolean('active')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trips');
    }
};
