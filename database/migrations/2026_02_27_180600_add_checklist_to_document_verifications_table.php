<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('document_verifications')) {
            return;
        }

        Schema::table('document_verifications', function (Blueprint $table): void {
            if (!Schema::hasColumn('document_verifications', 'checklist')) {
                $table->json('checklist')->nullable()->after('remark');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('document_verifications')) {
            return;
        }

        Schema::table('document_verifications', function (Blueprint $table): void {
            if (Schema::hasColumn('document_verifications', 'checklist')) {
                $table->dropColumn('checklist');
            }
        });
    }
};
