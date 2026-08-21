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

            $table->string('platform', 30);
            $table->string('platform_order_id', 100);

            $table->string('buyer_name', 255);
            $table->text('zip_code');
            $table->string('address', 255);

            $table->integer('item_count');
            $table->integer('items_subtotal');
            $table->integer('coupon_discount')->default(0);
            $table->integer('shipt_fee')->default(0);
            $table->integer('grand_total');

            $table->date('shipt_date');

            $table->timestamps();

            $table->unique(
                ['platform', 'platform_order_id'],
                'orders_platform_order_id_unique'
            );
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
