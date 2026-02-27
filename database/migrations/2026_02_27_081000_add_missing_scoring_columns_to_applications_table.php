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
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table): void {
            if (!Schema::hasColumn('applications', 'sub_reservation')) {
                $table->string('sub_reservation')->nullable()->after('category');
            }

            if (!Schema::hasColumn('applications', 'education_percentage')) {
                $table->decimal('education_percentage', 5, 2)->nullable()->after('status');
            }

            if (!Schema::hasColumn('applications', 'experience_marks')) {
                $table->decimal('experience_marks', 5, 2)->nullable()->after('education_percentage');
            }

            if (!Schema::hasColumn('applications', 'skill_marks')) {
                $table->decimal('skill_marks', 5, 2)->nullable()->after('experience_marks');
            }

            if (!Schema::hasColumn('applications', 'total_marks')) {
                $table->decimal('total_marks', 6, 2)->nullable()->after('skill_marks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table): void {
            $columns = ['sub_reservation', 'education_percentage', 'experience_marks', 'skill_marks', 'total_marks'];
            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('applications', $column)));
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
