<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('client_id')->nullable()->change();
            $table->string('guest_token', 64)->nullable()->unique()->after('public_token');
            $table->json('guest_client')->nullable()->after('guest_token');
            $table->timestamp('guest_expires_at')->nullable()->after('guest_client');
        });
    }

    public function down(): void
    {
        DB::table('proposals')
            ->whereNull('user_id')
            ->delete();

        Schema::table('proposals', function (Blueprint $table): void {
            $table->dropColumn([
                'guest_token',
                'guest_client',
                'guest_expires_at',
            ]);
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreignId('client_id')->nullable(false)->change();
        });
    }
};
