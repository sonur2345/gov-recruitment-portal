<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table): void {
            if (!Schema::hasColumn('posts', 'weight_education')) {
                $table->decimal('weight_education', 5, 2)->default(1)->after('skill_test_required');
            }
            if (!Schema::hasColumn('posts', 'weight_skill')) {
                $table->decimal('weight_skill', 5, 2)->default(1)->after('weight_education');
            }
            if (!Schema::hasColumn('posts', 'weight_experience')) {
                $table->decimal('weight_experience', 5, 2)->default(1)->after('weight_skill');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table): void {
            $columns = ['weight_education', 'weight_skill', 'weight_experience'];
            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('posts', $column)));
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
