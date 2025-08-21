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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Null for guest customers
            $table->unsignedBigInteger('country_id')->nullable();

            // Customer basic info
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            // Billing Address fields
            $table->unsignedBigInteger('billing_country_id')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_city')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_building_number')->nullable();

            // Shipping Address fields
            $table->unsignedBigInteger('shipping_country_id')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_city')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_building_number')->nullable();

            // Option to use billing for shipping
            $table->boolean('use_billing_for_shipping')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('billing_country_id')->references('id')->on('countries');
            $table->foreign('shipping_country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
