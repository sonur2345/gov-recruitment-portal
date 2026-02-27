<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('data');
            $table->string('pdf_path', 2048)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['notification_id', 'version'], 'notification_revisions_version_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_revisions');
    }
};
