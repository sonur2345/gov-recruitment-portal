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
        Schema::create('appointment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('reference_number')->unique();
            $table->date('issue_date');
            $table->date('joining_deadline');
            $table->text('office_address');
            $table->string('signature_name')->nullable();
            $table->string('pdf_path');
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_orders');
    }
};
