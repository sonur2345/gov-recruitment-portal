<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table): void {
            if (!Schema::hasColumn('notifications', 'postal_address')) {
                $table->text('postal_address')->nullable()->after('description');
            }
            if (!Schema::hasColumn('notifications', 'last_date_time')) {
                $table->dateTime('last_date_time')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('notifications', 'dd_payee_text')) {
                $table->string('dd_payee_text', 255)->nullable()->after('last_date_time');
            }
            if (!Schema::hasColumn('notifications', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table): void {
            $columns = ['postal_address', 'last_date_time', 'dd_payee_text', 'version'];
            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('notifications', $column)));
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
