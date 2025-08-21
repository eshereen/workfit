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
        Schema::table('customers', function (Blueprint $table) {
            // Customer information (if not already present)
            $table->string('first_name')->nullable()->after('email');
            $table->string('last_name')->nullable()->after('first_name');

            // Billing Address
            $table->foreignId('billing_country_id')->nullable()->constrained('countries')->after('country_id');
            $table->string('billing_state')->nullable()->after('billing_country_id');
            $table->string('billing_city')->nullable()->after('billing_state');
            $table->text('billing_address')->nullable()->after('billing_city');
            $table->string('billing_building_number')->nullable()->after('billing_address');

            // Shipping Address
            $table->foreignId('shipping_country_id')->nullable()->constrained('countries')->after('billing_building_number');
            $table->string('shipping_state')->nullable()->after('shipping_country_id');
            $table->string('shipping_city')->nullable()->after('shipping_state');
            $table->text('shipping_address')->nullable()->after('shipping_city');
            $table->string('shipping_building_number')->nullable()->after('shipping_address');

            // Option to use billing for shipping
            $table->boolean('use_billing_for_shipping')->default(false)->after('shipping_building_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['billing_country_id']);
            $table->dropForeign(['shipping_country_id']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'billing_country_id',
                'billing_state',
                'billing_city',
                'billing_address',
                'billing_building_number',
                'shipping_country_id',
                'shipping_state',
                'shipping_city',
                'shipping_address',
                'shipping_building_number',
                'use_billing_for_shipping'
            ]);
        });
    }
};
