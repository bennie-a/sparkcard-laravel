<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return voidmigr
     */
    public function up()
    {
        Schema::create('shipping_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id')->notNull();
            $table->string('name')->notNull();
            $table->string('zip_code', 8)->notNull();
            $table->string('address')->notNull();
            $table->integer('stock_id')->unsigned();
            $table->foreign('stock_id')->references('id')->on('stockpile');
            $table->integer('quantity')->notNull();
            $table->integer('single_price')->notNull();
            $table->integer('total_price')->notNull();
            $table->date('shipping_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_log');
    }
};
