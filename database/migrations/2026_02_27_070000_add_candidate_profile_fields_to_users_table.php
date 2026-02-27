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
        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'father_name')) {
                $table->string('father_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'mother_name')) {
                $table->string('mother_name')->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('mother_name');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 20)->nullable()->after('dob');
            }
            if (!Schema::hasColumn('users', 'category')) {
                $table->string('category', 20)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'marital_status')) {
                $table->string('marital_status', 30)->nullable()->after('category');
            }
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality', 60)->nullable()->after('marital_status');
            }
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile', 20)->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('users', 'correspondence_address')) {
                $table->text('correspondence_address')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('users', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('correspondence_address');
            }
            if (!Schema::hasColumn('users', 'aadhaar_number')) {
                $table->string('aadhaar_number', 255)->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('users', 'id_proof_path')) {
                $table->string('id_proof_path')->nullable()->after('aadhaar_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $columns = [
                'father_name',
                'mother_name',
                'dob',
                'gender',
                'category',
                'marital_status',
                'nationality',
                'mobile',
                'correspondence_address',
                'permanent_address',
                'aadhaar_number',
                'id_proof_path',
            ];

            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('users', $column)));

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
