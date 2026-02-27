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
        if (!Schema::hasTable('applications') || Schema::hasColumn('applications', 'rank')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table): void {
            if (Schema::hasColumn('applications', 'total_marks')) {
                $table->unsignedInteger('rank')->nullable()->after('total_marks');
            } else {
                $table->unsignedInteger('rank')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'rank')) {
                $table->dropColumn('rank');
            }
        });
    }
};
