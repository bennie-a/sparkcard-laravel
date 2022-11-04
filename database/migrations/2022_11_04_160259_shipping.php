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
        Schema::create('shipping', function(Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('notion_id', 32)->nullable(false)->comment('NotionのID');
            $table->string('name', 100)->nullable(false)->comment('発送方法');
            $table->integer('price')->nullable(false)->comment('送料');
            $table->timestamps();

            // ユニークキーの追加
            $table->unique(['notion_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping');
    }
};
