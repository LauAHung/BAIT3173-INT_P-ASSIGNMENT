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
        Schema::create('Journeys', function (Blueprint $table) {
            $table->string('JourneyID')->primary();
            $table->string('TrainID');
            $table->string('FromLocation', 100);
            $table->string('ToLocation', 100);
            $table->timestamp('DepartureTime');
            $table->timestamp('ArrivalTime');
            $table->integer('SeatAvailable');
            $table->decimal('Price', 10, 2);
            $table->enum('Status', ['Scheduled', 'Delayed', 'Canceled'])->default('Scheduled');
            $table->timestamp('Created_at')->nullable();
            
            $table->foreign('TrainID')->references('TrainID')->on('Trains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Journeys');
    }
};
