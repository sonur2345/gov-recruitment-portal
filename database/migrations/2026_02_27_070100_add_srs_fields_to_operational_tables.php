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
        Schema::table('notifications', function (Blueprint $table): void {
            if (!Schema::hasColumn('notifications', 'advertisement_no')) {
                $table->string('advertisement_no', 120)->nullable()->after('title');
            }
            if (!Schema::hasColumn('notifications', 'fee_last_date')) {
                $table->date('fee_last_date')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('notifications', 'exam_date')) {
                $table->date('exam_date')->nullable()->after('fee_last_date');
            }
            if (!Schema::hasColumn('notifications', 'helpdesk_phone')) {
                $table->string('helpdesk_phone', 50)->nullable()->after('exam_date');
            }
            if (!Schema::hasColumn('notifications', 'helpdesk_email')) {
                $table->string('helpdesk_email', 255)->nullable()->after('helpdesk_phone');
            }
        });

        Schema::table('posts', function (Blueprint $table): void {
            if (!Schema::hasColumn('posts', 'pay_level')) {
                $table->string('pay_level', 100)->nullable()->after('qualification_text');
            }
            if (!Schema::hasColumn('posts', 'application_fee_general')) {
                $table->decimal('application_fee_general', 10, 2)->default(500)->after('pay_level');
            }
            if (!Schema::hasColumn('posts', 'application_fee_reserved')) {
                $table->decimal('application_fee_reserved', 10, 2)->default(0)->after('application_fee_general');
            }
            if (!Schema::hasColumn('posts', 'exam_date')) {
                $table->date('exam_date')->nullable()->after('application_fee_reserved');
            }
        });

        Schema::table('applications', function (Blueprint $table): void {
            if (!Schema::hasColumn('applications', 'pwbd_status')) {
                if (Schema::hasColumn('applications', 'sub_reservation')) {
                    $table->boolean('pwbd_status')->default(false)->after('sub_reservation');
                } else {
                    $table->boolean('pwbd_status')->default(false);
                }
            }
            if (!Schema::hasColumn('applications', 'ex_serviceman')) {
                if (Schema::hasColumn('applications', 'pwbd_status')) {
                    $table->boolean('ex_serviceman')->default(false)->after('pwbd_status');
                } else {
                    $table->boolean('ex_serviceman')->default(false);
                }
            }
        });

        Schema::table('demand_drafts', function (Blueprint $table): void {
            if (!Schema::hasColumn('demand_drafts', 'bank_branch')) {
                $table->string('bank_branch', 255)->nullable()->after('bank_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            $columns = ['advertisement_no', 'fee_last_date', 'exam_date', 'helpdesk_phone', 'helpdesk_email'];
            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('notifications', $column)));
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });

        Schema::table('posts', function (Blueprint $table): void {
            $columns = ['pay_level', 'application_fee_general', 'application_fee_reserved', 'exam_date'];
            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('posts', $column)));
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });

        Schema::table('applications', function (Blueprint $table): void {
            $columns = ['pwbd_status', 'ex_serviceman'];
            $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('applications', $column)));
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });

        Schema::table('demand_drafts', function (Blueprint $table): void {
            if (Schema::hasColumn('demand_drafts', 'bank_branch')) {
                $table->dropColumn('bank_branch');
            }
        });
    }
};
