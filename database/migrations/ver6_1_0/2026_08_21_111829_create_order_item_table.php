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

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('order_id');
            $table->unsignedInteger('stock_id');

            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('subtotal');

            $table->timestamp('created_at', 0)->nullable();
            $table->timestamp('updated_at', 0)->nullable();

            $table->foreign('order_id', 'order_items_order_id_fkey')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('stock_id', 'order_items_stock_id_fkey')
                ->references('id')
                ->on('stockpile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
