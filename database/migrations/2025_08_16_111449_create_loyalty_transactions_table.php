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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->integer('points'); // Positive for earned, negative for redeemed
            $table->string('action'); // 'purchase', 'signup', 'redeem', etc.
            $table->string('description')->nullable();
            $table->morphs('source'); // Polymorphic relation (e.g., Order, User)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyality_transactions');
    }
};
