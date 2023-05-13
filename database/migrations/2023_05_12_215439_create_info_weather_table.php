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
        Schema::create('info_weather', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_city',255);
            $table->decimal('latitude', 6, 4);
            $table->decimal('longitude', 6, 4);
            $table->decimal('temp',5,2);
            $table->decimal('feels_like',5,2);
            $table->decimal('temp_min',5,2);
            $table->decimal('temp_max',5,2);
            $table->integer('pressure');
            $table->integer('humidity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_weather');
    }
};
