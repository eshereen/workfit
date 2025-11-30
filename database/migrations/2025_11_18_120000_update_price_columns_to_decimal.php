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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->change();
            $table->decimal('compare_price', 12, 2)->nullable()->change();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('tax_amount', 15, 2)->default(0)->change();
            $table->decimal('shipping_amount', 15, 2)->default(0)->change();
            $table->decimal('discount_amount', 15, 2)->default(0)->change();
            $table->decimal('total_amount', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('subtotal')->change();
            $table->integer('tax_amount')->default(0)->change();
            $table->integer('shipping_amount')->default(0)->change();
            $table->integer('discount_amount')->default(0)->change();
            $table->integer('total_amount')->change();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('price')->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('price')->change();
            $table->integer('compare_price')->nullable()->change();
        });
    }
};

