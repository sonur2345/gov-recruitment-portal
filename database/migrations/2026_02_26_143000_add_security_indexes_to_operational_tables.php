<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['status', 'start_date', 'end_date'], 'notifications_status_window_idx');
            });
        }

        if (Schema::hasTable('demand_drafts')) {
            Schema::table('demand_drafts', function (Blueprint $table) {
                $table->index(['status', 'application_id'], 'demand_drafts_status_application_idx');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->index(['model_type', 'model_id'], 'audit_logs_model_type_model_id_idx');
            });
        }

        if (Schema::hasTable('two_factor_otps')) {
            Schema::table('two_factor_otps', function (Blueprint $table) {
                $table->index(['user_id', 'expires_at'], 'two_factor_otps_user_expiry_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('notifications_status_window_idx');
            });
        }

        if (Schema::hasTable('demand_drafts')) {
            Schema::table('demand_drafts', function (Blueprint $table) {
                $table->dropIndex('demand_drafts_status_application_idx');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropIndex('audit_logs_model_type_model_id_idx');
            });
        }

        if (Schema::hasTable('two_factor_otps')) {
            Schema::table('two_factor_otps', function (Blueprint $table) {
                $table->dropIndex('two_factor_otps_user_expiry_idx');
            });
        }
    }
};
