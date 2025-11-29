<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('admin_activity_logs')) {
            return;
        }

        // Fix MySQL tables created without AUTO_INCREMENT on `id`
        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE `admin_activity_logs` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            } else {
                // Best-effort for non-MySQL drivers; may require doctrine/dbal for change()
                Schema::table('admin_activity_logs', function (Blueprint $table) {
                    // $table->bigIncrements('id')->change(); // Uncomment if dbal is available
                });
            }
        } catch (\Throwable $e) {
            // Silent no-op to avoid breaking migrations in other environments
        }
    }

    public function down(): void
    {
        // No rollback needed; keeping AUTO_INCREMENT is safe.
    }
};









