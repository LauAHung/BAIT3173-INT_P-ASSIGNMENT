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
        Schema::create('Trains', function (Blueprint $table) {
            $table->string('TrainID')->primary();
            $table->string('StationID');
            $table->string('TrainNo', 50);
            $table->string('TrainService', 100);
            $table->integer('SeatCount');
            $table->enum('Is_available', ['Active', 'Unavailable'])->default('Active');
            $table->timestamp('Created_at')->nullable();
            
            $table->foreign('StationID')->references('StationID')->on('Stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Trains');
    }
};
