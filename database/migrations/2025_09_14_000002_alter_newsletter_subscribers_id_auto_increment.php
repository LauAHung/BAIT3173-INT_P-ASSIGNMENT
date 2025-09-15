<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure the `id` column is AUTO_INCREMENT (MySQL)
        $driver = config('database.default');
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `newsletter_subscribers` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        // Best-effort revert: remove AUTO_INCREMENT flag (keeps NOT NULL)
        $driver = config('database.default');
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `newsletter_subscribers` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        }
    }
};


