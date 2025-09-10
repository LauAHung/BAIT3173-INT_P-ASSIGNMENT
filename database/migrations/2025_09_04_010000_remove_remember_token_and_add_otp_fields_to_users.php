<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (!Schema::hasColumn('users', 'password_reset_token')) {
                $table->string('password_reset_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'password_reset_expires_at')) {
                $table->timestamp('password_reset_expires_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'password_reset_otp')) {
                $table->string('password_reset_otp', 6)->nullable();
            }
            if (!Schema::hasColumn('users', 'password_reset_otp_expires_at')) {
                $table->timestamp('password_reset_otp_expires_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
            if (Schema::hasColumn('users', 'password_reset_otp')) {
                $table->dropColumn(['password_reset_otp', 'password_reset_otp_expires_at']);
            }
        });
    }
};


