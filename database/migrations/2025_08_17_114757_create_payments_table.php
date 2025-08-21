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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // 'paymob','paypal','cod'
            $table->string('provider_reference')->nullable(); // intent id, paymob order id, paypal capture id
            $table->string('status')->default('initiated'); // initiated, pending_redirect, succeeded, failed, canceled
            $table->string('currency', 3); // e.g. EGP, USD, AED
            $table->unsignedBigInteger('amount_minor'); // amount in minor units for precision
            $table->string('return_url')->nullable();
            $table->string('cancel_url')->nullable();
            $table->string('webhook_signature')->nullable(); // for verifying (optional)
            $table->json('meta')->nullable(); // anything gateway-specific
            $table->timestamps();
            $table->index(['provider','provider_reference']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
