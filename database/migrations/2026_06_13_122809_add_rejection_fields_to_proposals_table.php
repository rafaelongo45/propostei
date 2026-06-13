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
        Schema::table('proposals', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('accepted_by_ip');
            $table->string('rejected_by_name')->nullable()->after('rejected_at');
            $table->string('rejected_by_email')->nullable()->after('rejected_by_name');
            $table->string('rejected_by_ip', 45)->nullable()->after('rejected_by_email');
            $table->text('rejection_reason')->nullable()->after('rejected_by_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn([
                'rejected_at',
                'rejected_by_name',
                'rejected_by_email',
                'rejected_by_ip',
                'rejection_reason',
            ]);
        });
    }
};
