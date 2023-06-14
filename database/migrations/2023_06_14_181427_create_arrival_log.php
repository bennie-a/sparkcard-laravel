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
        Schema::create('arrival_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('supplier')->notNull();
            $table->date('arrival_date')->notNull();
            $table->integer('stock_id')->unsigned()->notNull();
            $table->foreign('stock_id')->references('id')->on('stockpile');
            $table->integer('quantity')->notNull();
            $table->timestamps();
            $table->unique(['supplier', 'arrival_date', 'stock_id', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arrival_log');
    }
};
