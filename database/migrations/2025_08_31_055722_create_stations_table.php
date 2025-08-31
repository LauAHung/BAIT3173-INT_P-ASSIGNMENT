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
        Schema::create('Stations', function (Blueprint $table) {
            $table->string('StationID')->primary();
            $table->string('StationName', 100);
            $table->string('Location', 255);
            $table->boolean('Is_active')->default(true);
            $table->timestamp('Created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Stations');
    }
};
