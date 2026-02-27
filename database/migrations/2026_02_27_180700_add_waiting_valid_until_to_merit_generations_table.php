<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('merit_generations')) {
            return;
        }

        Schema::table('merit_generations', function (Blueprint $table): void {
            if (!Schema::hasColumn('merit_generations', 'waiting_valid_until')) {
                $table->date('waiting_valid_until')->nullable()->after('selected_count');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('merit_generations')) {
            return;
        }

        Schema::table('merit_generations', function (Blueprint $table): void {
            if (Schema::hasColumn('merit_generations', 'waiting_valid_until')) {
                $table->dropColumn('waiting_valid_until');
            }
        });
    }
};
