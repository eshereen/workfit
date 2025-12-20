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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('button_text_2')->nullable()->after('custom_url');
            $table->string('link_type_2')->default('none')->after('button_text_2');
            $table->unsignedBigInteger('category_id_2')->nullable()->after('link_type_2');
            $table->unsignedBigInteger('subcategory_id_2')->nullable()->after('category_id_2');
            $table->string('custom_url_2')->nullable()->after('subcategory_id_2');

            // Foreign keys if you want them, or just rely on application logic like the first ones might be doing
            // Assuming simplified structure for now as per likely existing pattern
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'button_text_2',
                'link_type_2',
                'category_id_2',
                'subcategory_id_2',
                'custom_url_2'
            ]);
        });
    }
};
