<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table): void {
            $table->timestamp('canceled_at')->nullable()->after('rejection_reason');
            $table->text('canceled_reason')->nullable()->after('canceled_at');
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table): void {
            $table->dropColumn([
                'canceled_at',
                'canceled_reason',
            ]);
        });
    }
};
