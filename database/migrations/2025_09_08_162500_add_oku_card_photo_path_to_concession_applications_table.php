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
        Schema::table('concession_applications', function (Blueprint $table) {
            $table->string('oku_card_photo_path')->nullable()->after('student_id_photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concession_applications', function (Blueprint $table) {
            $table->dropColumn('oku_card_photo_path');
        });
    }
};







