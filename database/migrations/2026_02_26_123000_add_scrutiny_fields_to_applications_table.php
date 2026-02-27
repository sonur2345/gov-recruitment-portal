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
            $table->enum('scrutiny_decision', ['eligible', 'not_eligible', 'pending'])
                ->nullable()
                ->after('status');
            $table->text('scrutiny_remark')->nullable()->after('scrutiny_decision');
            $table->foreignId('scrutiny_officer_id')
                ->nullable()
                ->after('scrutiny_remark')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('scrutiny_officer_id');
            $table->dropColumn(['scrutiny_decision', 'scrutiny_remark']);
        });
    }
};
