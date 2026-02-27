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
        if (Schema::hasTable('applications')) {
            return;
        }

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->string('sub_reservation')->nullable();
            $table->date('dob');
            $table->enum('status', [
                'submitted',
                'dd_verified',
                'under_scrutiny',
                'eligible',
                'rejected',
                'shortlisted',
                'appeared',
                'qualified',
                'dv_pending',
                'selected',
                'waiting'
            ])->default('submitted');
            $table->decimal('education_percentage', 5, 2)->nullable();
            $table->decimal('experience_marks', 5, 2)->nullable();
            $table->decimal('skill_marks', 5, 2)->nullable();
            $table->decimal('total_marks', 6, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::dropIfExists('applications');
        }
    }
};
