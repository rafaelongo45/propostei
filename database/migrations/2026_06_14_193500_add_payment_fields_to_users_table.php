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
        Schema::table('users', function (Blueprint $table) {
            $table->string('payment_pix_key')->nullable()->after('company_logo_path');
            $table->string('payment_pix_key_type', 40)->nullable()->after('payment_pix_key');
            $table->string('payment_recipient_name')->nullable()->after('payment_pix_key_type');
            $table->string('payment_link', 2048)->nullable()->after('payment_recipient_name');
            $table->text('payment_instructions')->nullable()->after('payment_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'payment_pix_key',
                'payment_pix_key_type',
                'payment_recipient_name',
                'payment_link',
                'payment_instructions',
            ]);
        });
    }
};
