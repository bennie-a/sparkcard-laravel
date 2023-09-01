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
        Schema::table('card_info', function (Blueprint $table) {
            $table->integer('foiltype_id')->default(1)->comment('表面加工ID');
            // 外部キー制約
            $table->foreign('foiltype_id')->references('id')->on('foiltype');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_info', function (Blueprint $table) {    
            $table->dropColumn('treat_id');
        });
    }
};
