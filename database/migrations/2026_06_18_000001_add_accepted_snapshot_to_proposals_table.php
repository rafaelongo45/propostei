<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table): void {
            $table->json('accepted_snapshot')->nullable()->after('acceptance_notes');
            $table->string('accepted_snapshot_hash')->nullable()->after('accepted_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table): void {
            $table->dropColumn([
                'accepted_snapshot',
                'accepted_snapshot_hash',
            ]);
        });
    }
};
