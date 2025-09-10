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
        Schema::create('concession_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->unique(); // Auto-generated ID like APP001
            $table->enum('type', ['oku', 'senior', 'student']); // Application type
            $table->string('full_name');
            $table->string('ic_number');
            $table->string('citizenship');
            
            // OKU specific fields
            $table->string('passport_number')->nullable();
            $table->string('oku_card_number')->nullable();
            $table->text('disability_info')->nullable();
            
            // Senior citizen specific fields
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Student specific fields
            $table->string('matrix_number')->nullable();
            $table->string('education_level')->nullable();
            $table->string('school_name')->nullable();
            $table->string('student_id_photo_path')->nullable();
            
            // OKU specific fields
            $table->string('oku_card_photo_path')->nullable();
            
            // Senior citizen specific fields
            $table->string('senior_ic_photo_path')->nullable();
            
            // Application status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Admin user ID
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'type']);
            $table->index('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concession_applications');
    }
};
