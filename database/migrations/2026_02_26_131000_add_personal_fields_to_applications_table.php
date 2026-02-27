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
            if (!Schema::hasColumn('applications', 'gender')) {
                $table->string('gender', 20)->nullable()->after('dob');
            }
            if (!Schema::hasColumn('applications', 'father_name')) {
                $table->string('father_name')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('applications', 'mobile')) {
                $table->string('mobile', 20)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('applications', 'address')) {
                $table->text('address')->nullable()->after('mobile');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $toDrop = [];
            foreach (['gender', 'father_name', 'mobile', 'address'] as $column) {
                if (Schema::hasColumn('applications', $column)) {
                    $toDrop[] = $column;
                }
            }
            if ($toDrop !== []) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
