<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('demand_drafts')) {
            return;
        }

        Schema::table('demand_drafts', function (Blueprint $table): void {
            if (!Schema::hasColumn('demand_drafts', 'dd_scan_path')) {
                $table->string('dd_scan_path', 2048)->nullable()->after('bank_branch');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('demand_drafts')) {
            return;
        }

        Schema::table('demand_drafts', function (Blueprint $table): void {
            if (Schema::hasColumn('demand_drafts', 'dd_scan_path')) {
                $table->dropColumn('dd_scan_path');
            }
        });
    }
};
