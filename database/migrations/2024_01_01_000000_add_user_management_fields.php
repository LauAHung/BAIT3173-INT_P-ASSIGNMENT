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
        Schema::table('users', function (Blueprint $table) {
            // Add new fields for comprehensive user management
            $table->string('profile_picture')->nullable()->after('password');
            $table->string('social_provider')->nullable()->after('profile_picture');
            $table->string('social_provider_id')->nullable()->after('social_provider');
            $table->json('social_provider_data')->nullable()->after('social_provider_id');
            $table->json('email_subscription')->nullable()->after('social_provider_data');
            $table->string('email_verification_token')->nullable()->after('email_subscription');
            $table->string('account_status')->default('active')->after('email_verification_token');
            $table->timestamp('last_login_at')->nullable()->after('account_status');
            $table->string('password_reset_token')->nullable()->after('last_login_at');
            $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_token');
            
            // Add indexes for better performance
            $table->index(['social_provider', 'social_provider_id']);
            $table->index('account_status');
            $table->index('email_verification_token');
            $table->index('password_reset_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['social_provider', 'social_provider_id']);
            $table->dropIndex(['account_status']);
            $table->dropIndex(['email_verification_token']);
            $table->dropIndex(['password_reset_token']);
            
            // Drop columns
            $table->dropColumn([
                'profile_picture',
                'social_provider',
                'social_provider_id',
                'social_provider_data',
                'email_subscription',
                'email_verification_token',
                'account_status',
                'last_login_at',
                'password_reset_token',
                'password_reset_expires_at'
            ]);
        });
    }
}; 