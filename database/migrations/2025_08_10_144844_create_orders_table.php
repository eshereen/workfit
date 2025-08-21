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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // For guest orders
            $table->unsignedBigInteger('user_id')->nullable(); // Only for registered users
            $table->string('guest_token')->nullable()->unique(); // For guest orders
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->unsignedBigInteger(column: 'country_id')->nullable()->constrained();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('email')->nullable(); // For guest orders
            $table->string('phone_number')->nullable(); // For guest orders
            $table->integer('subtotal');
            $table->integer('tax_amount')->default(0);
            $table->integer('shipping_amount')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('total_amount');
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method');

            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->json('billing_address');
            $table->json('shipping_address');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_guest')->default(true);
            $table->integer('loyalty_points_used')->default(0);
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
