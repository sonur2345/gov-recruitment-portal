<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'name')) {
                $table->string('name')->nullable()->after('notification_id');
            }

            if (!Schema::hasColumn('posts', 'code')) {
                $table->string('code')->nullable()->after('name');
            }

            if (!Schema::hasColumn('posts', 'total_vacancies')) {
                $table->integer('total_vacancies')->nullable()->after('code');
            }

            if (!Schema::hasColumn('posts', 'category_breakup')) {
                $table->json('category_breakup')->nullable()->after('total_vacancies');
            }

            if (!Schema::hasColumn('posts', 'age_min')) {
                $table->integer('age_min')->nullable()->after('category_breakup');
            }

            if (!Schema::hasColumn('posts', 'age_max')) {
                $table->integer('age_max')->nullable()->after('age_min');
            }

            if (!Schema::hasColumn('posts', 'qualification_text')) {
                $table->text('qualification_text')->nullable()->after('age_max');
            }

            if (!Schema::hasColumn('posts', 'experience_required')) {
                $table->boolean('experience_required')->default(false)->after('qualification_text');
            }

            if (!Schema::hasColumn('posts', 'skill_test_required')) {
                $table->boolean('skill_test_required')->default(false)->after('experience_required');
            }
        });

        if (Schema::hasColumn('posts', 'title') && Schema::hasColumn('posts', 'name')) {
            DB::table('posts')->whereNull('name')->update([
                'name' => DB::raw('title'),
            ]);
        }

        if (Schema::hasColumn('posts', 'total_posts') && Schema::hasColumn('posts', 'total_vacancies')) {
            DB::table('posts')->whereNull('total_vacancies')->update([
                'total_vacancies' => DB::raw('total_posts'),
            ]);
        }

        if (Schema::hasColumn('posts', 'category_breakup')) {
            DB::table('posts')->whereNull('category_breakup')->update([
                'category_breakup' => '{}',
            ]);
        }

        if (Schema::hasColumn('posts', 'age_min')) {
            DB::table('posts')->whereNull('age_min')->update([
                'age_min' => 18,
            ]);
        }

        if (Schema::hasColumn('posts', 'age_max')) {
            DB::table('posts')->whereNull('age_max')->update([
                'age_max' => 60,
            ]);
        }

        if (Schema::hasColumn('posts', 'qualification_text')) {
            DB::table('posts')->whereNull('qualification_text')->update([
                'qualification_text' => 'Not specified',
            ]);
        }

        if (Schema::hasColumn('posts', 'code')) {
            DB::table('posts')
                ->select('id')
                ->where(function ($query): void {
                    $query->whereNull('code')->orWhere('code', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows): void {
                    foreach ($rows as $row) {
                        DB::table('posts')
                            ->where('id', $row->id)
                            ->update([
                                'code' => 'LEGACY-' . str_pad((string) $row->id, 6, '0', STR_PAD_LEFT),
                            ]);
                    }
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback to avoid destructive data loss in live tables.
    }
};
