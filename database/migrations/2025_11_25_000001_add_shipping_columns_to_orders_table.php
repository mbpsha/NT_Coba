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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('total_harga');
            $table->decimal('admin_fee', 12, 2)->default(0)->after('shipping_cost');
            $table->integer('shipping_weight')->default(1000)->after('admin_fee');
            $table->unsignedInteger('shipping_destination_city_id')->nullable()->after('shipping_weight');
            $table->string('shipping_courier', 20)->nullable()->after('shipping_destination_city_id');
            $table->string('shipping_service', 50)->nullable()->after('shipping_courier');
            $table->string('shipping_etd', 20)->nullable()->after('shipping_service');
            $table->boolean('shipping_is_estimated')->default(true)->after('shipping_etd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_cost',
                'admin_fee',
                'shipping_weight',
                'shipping_destination_city_id',
                'shipping_courier',
                'shipping_service',
                'shipping_etd',
                'shipping_is_estimated',
            ]);
        });
    }
};


