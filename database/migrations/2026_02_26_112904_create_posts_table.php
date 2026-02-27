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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->integer('total_vacancies');
            $table->json('category_breakup');
            $table->integer('age_min');
            $table->integer('age_max');
            $table->text('qualification_text');
            $table->boolean('experience_required');
            $table->boolean('skill_test_required');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
