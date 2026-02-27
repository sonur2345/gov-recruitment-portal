<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE applications
            MODIFY status ENUM(
                'submitted',
                'dd_verified',
                'under_scrutiny',
                'eligible',
                'rejected',
                'shortlisted',
                'appeared',
                'qualified',
                'dv_pending',
                'selected',
                'waiting',
                'final_selected'
            ) NOT NULL DEFAULT 'submitted'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            UPDATE applications
            SET status = 'selected'
            WHERE status = 'final_selected'
        ");

        DB::statement("
            ALTER TABLE applications
            MODIFY status ENUM(
                'submitted',
                'dd_verified',
                'under_scrutiny',
                'eligible',
                'rejected',
                'shortlisted',
                'appeared',
                'qualified',
                'dv_pending',
                'selected',
                'waiting'
            ) NOT NULL DEFAULT 'submitted'
        ");
    }
};
