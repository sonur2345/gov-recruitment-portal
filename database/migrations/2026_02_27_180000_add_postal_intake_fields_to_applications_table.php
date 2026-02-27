<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table): void {
            if (!Schema::hasColumn('applications', 'source')) {
                $table->enum('source', ['online', 'postal'])->default('online')->after('status');
            }
            if (!Schema::hasColumn('applications', 'inward_no')) {
                $table->string('inward_no', 120)->nullable()->after('source');
            }
            if (!Schema::hasColumn('applications', 'inward_date')) {
                $table->date('inward_date')->nullable()->after('inward_no');
            }
            if (!Schema::hasColumn('applications', 'postal_received_at')) {
                $table->dateTime('postal_received_at')->nullable()->after('inward_date');
            }
            if (!Schema::hasColumn('applications', 'envelope_scan_path')) {
                $table->string('envelope_scan_path', 2048)->nullable()->after('postal_received_at');
            }
        });

        Schema::table('applications', function (Blueprint $table): void {
            if (Schema::hasColumn('applications', 'source') && Schema::hasColumn('applications', 'inward_no')) {
                $table->index(['source', 'inward_no'], 'applications_source_inward_no_idx');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table): void {
            if (Schema::hasColumn('applications', 'source') && Schema::hasColumn('applications', 'inward_no')) {
                $table->dropIndex('applications_source_inward_no_idx');
            }

            $columns = ['source', 'inward_no', 'inward_date', 'postal_received_at', 'envelope_scan_path'];
            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('applications', $column)));
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
