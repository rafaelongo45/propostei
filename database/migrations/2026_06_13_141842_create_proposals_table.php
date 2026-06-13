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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('introduction')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('delivery_estimate')->nullable();
            $table->string('status')->default('draft');
            $table->text('internal_notes')->nullable();
            $table->string('public_token')->unique();
            $table->decimal('manual_discount', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
