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
        Schema::table('applications', function (Blueprint $table) {
            $table->index(['post_id', 'status'], 'applications_post_status_idx');
            $table->index(['status', 'created_at'], 'applications_status_created_idx');
            $table->index('category', 'applications_category_idx');
            $table->index('created_at', 'applications_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('applications_post_status_idx');
            $table->dropIndex('applications_status_created_idx');
            $table->dropIndex('applications_category_idx');
            $table->dropIndex('applications_created_at_idx');
        });
    }
};
