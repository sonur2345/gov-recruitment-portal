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
            if (!Schema::hasColumn('applications', 'aadhar_number')) {
                $table->text('aadhar_number')->nullable()->after('address');
            }
            if (!Schema::hasColumn('applications', 'bank_account_number')) {
                $table->text('bank_account_number')->nullable()->after('aadhar_number');
            }
            if (!Schema::hasColumn('applications', 'ifsc_code')) {
                $table->string('ifsc_code', 20)->nullable()->after('bank_account_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $drop = [];
            foreach (['aadhar_number', 'bank_account_number', 'ifsc_code'] as $column) {
                if (Schema::hasColumn('applications', $column)) {
                    $drop[] = $column;
                }
            }

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
