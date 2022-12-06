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
        Schema::create('base_category', function(Blueprint $table) {
            $table->id();
            $table->string('exp_id', 36)->references('notion_id')->on('expansion')->comment('expansionテーブルのID');
            $table->string('color_id')->references('attr')->on('main_color')->comment('main_colorテーブルの略称');
            $table->integer('base_id')->nullable(true)->comment('BASEのID');
            $table->timestamps();


            $table->unique(['exp_id', 'color_id']);
        });

        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('base_category');
    }
};
