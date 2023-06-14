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
        Schema::create('stockpile', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('card_id')->unsigned();
            $table->string('language', 5)->notNull();
            $table->string('condition', 5)->notNull();
            $table->integer('quantity')->notNull();
            $table->timestamps();
            
            // 外部キー制約
            $table->foreign('card_id')->references('id')->on('card_info')->onDelete('cascade');
            // 複合ユニークキー制約
           $table->unique(['card_id', 'language', 'condition']); 
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockpile');
    }
};
