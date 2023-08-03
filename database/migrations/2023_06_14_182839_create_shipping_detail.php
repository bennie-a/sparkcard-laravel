<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_id')->unsigned();
            $table->foreign('shipping_id')->references('id')->on('shipping_log');
            $table->integer('stock_id')->unsigned();
            $table->foreign('stock_id')->references('id')->on('stockpile');
            $table->integer('quantity')->notNull();
            $table->integer('single_price')->notNull();
            $table->integer('total_price')->notNull();
            $table->timestamp('created_at')->notNull();
            $table->timestamp('updated_at')->notNull();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_detail');
    }
};
