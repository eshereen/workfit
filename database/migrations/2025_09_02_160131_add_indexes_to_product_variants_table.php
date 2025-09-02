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
            // Add individual indexes for better query performance (safer approach)
            try {
                $table->index(['product_id'], 'product_variants_product_id_index');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['color'], 'product_variants_color_index');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['size'], 'product_variants_size_index');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['stock'], 'product_variants_stock_index');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['sku'], 'product_variants_sku_index');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['price'], 'product_variants_price_index');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Drop indexes safely
            try {
                $table->dropIndex('product_variants_product_id_index');
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex('product_variants_color_index');
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex('product_variants_size_index');
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex('product_variants_stock_index');
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex('product_variants_sku_index');
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex('product_variants_price_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });
    }
};
