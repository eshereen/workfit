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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->constrained()->cascadeOnDelete();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('sku')->unique(); // Unique SKU foeach variant
            $table->integer('stock')->default(0);
            $table->integer('price')->nullable(); // Optional: variant-specific pricing
            $table->integer('weight')->nullable(); // Optional weight for shipping calculations
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
