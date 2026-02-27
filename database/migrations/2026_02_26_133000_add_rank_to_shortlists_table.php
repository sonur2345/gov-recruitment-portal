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
        Schema::table('shortlists', function (Blueprint $table) {
            if (!Schema::hasColumn('shortlists', 'rank')) {
                $table->unsignedInteger('rank')->nullable()->after('application_id');
            }
        });

        // Backfill rank for existing records per post in creation order.
        $rows = DB::table('shortlists')
            ->select('id', 'post_id')
            ->orderBy('post_id')
            ->orderBy('id')
            ->get()
            ->groupBy('post_id');

        foreach ($rows as $postRows) {
            $rank = 1;
            foreach ($postRows as $row) {
                DB::table('shortlists')
                    ->where('id', $row->id)
                    ->update(['rank' => $rank++]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shortlists', function (Blueprint $table) {
            if (Schema::hasColumn('shortlists', 'rank')) {
                $table->dropColumn('rank');
            }
        });
    }
};
