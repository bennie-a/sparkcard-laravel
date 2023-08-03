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
        Schema::create('card_info', function(Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('barcode', 16)->nullable(false)->comment('ロジクラのバーコード');
            $table->string('number')->nullable(false)->comment('カード番号');
            $table->string('exp_id')->comment('expansionテーブルのID');
            $table->foreign('exp_id')->references('notion_id')->on('expansion');
            $table->string('name', 60)->nullable(false)->comment('カード名');
            $table->string('en_name', 60)->nullable(false)->comment('カード名(英語)');
            $table->string('color_id')->comment('main_colorテーブルの略称');
            $table->foreign('color_id')->references('attr')->on('main_color');
            $table->string('image_url')->nullable(true)->comment('画像URL');
            $table->boolean('isFoil')->nullable(false)->default(false)->comment('通常/Foil');
            $table->string('standard')->nullable(false)->default('not_legal')->comment('スタンダード使用許可');
            $table->string('pioneer')->nullable(false)->default('not_legal')->comment('パイオニア使用許可');
            $table->string('modarn')->nullable(false)->default('not_legal')->comment('モダン使用許可');
            $table->string('legacy')->nullable(false)->default('not_legal')->comment('レガシー使用許可');
            $table->string('vintage')->nullable(false)->default('not_legal')->comment('ヴィンテージ使用許可');
            $table->string('commander')->nullable(false)->default('not_legal')->comment('EDH使用許可');
            $table->string('pauper')->nullable(false)->default('not_legal')->comment('パウパー使用許可');
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
        Schema::dropIfExists('card_info');
    }
};
