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
        Schema::table('demand_drafts', function (Blueprint $table) {
            if (!Schema::hasColumn('demand_drafts', 'status')) {
                $table->enum('status', ['pending', 'valid', 'invalid'])->default('pending')->after('amount');
            }
            if (!Schema::hasColumn('demand_drafts', 'remark')) {
                $table->text('remark')->nullable()->after('status');
            }
            if (!Schema::hasColumn('demand_drafts', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->after('remark')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_drafts', function (Blueprint $table) {
            if (Schema::hasColumn('demand_drafts', 'admin_id')) {
                $table->dropConstrainedForeignId('admin_id');
            }
            if (Schema::hasColumn('demand_drafts', 'remark')) {
                $table->dropColumn('remark');
            }
            if (Schema::hasColumn('demand_drafts', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
