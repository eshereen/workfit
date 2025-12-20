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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            
            // Section identifier (hero, featured-1, featured-2, etc.)
            $table->string('section')->index();
            
            // Content
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image'); // Image path
            $table->string('button_text')->nullable(); // "Shop Now", "Explore", etc.
            
            // Link configuration
            $table->enum('link_type', ['category', 'subcategory', 'url', 'none'])->default('none');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->string('custom_url')->nullable();
            
            // Display settings
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            // Foreign keys
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
            
            // Indexes
            $table->index(['section', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
