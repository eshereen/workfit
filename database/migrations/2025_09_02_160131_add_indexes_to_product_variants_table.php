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
        Schema::table('product_variants', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index(['product_id', 'color'], 'product_variants_product_color_index');
            $table->index(['product_id', 'size'], 'product_variants_product_size_index');
            $table->index(['product_id', 'color', 'size'], 'product_variants_product_color_size_index');
            $table->index(['stock'], 'product_variants_stock_index');
            $table->index(['sku'], 'product_variants_sku_index');
            $table->index(['price'], 'product_variants_price_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_product_color_index');
            $table->dropIndex('product_variants_product_size_index');
            $table->dropIndex('product_variants_product_color_size_index');
            $table->dropIndex('product_variants_stock_index');
            $table->dropIndex('product_variants_sku_index');
            $table->dropIndex('product_variants_price_index');
        });
    }
};
