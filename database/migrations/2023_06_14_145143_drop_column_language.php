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
            $table->dropColumn("language");
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
            $table->string('language', 10)->nullable(false)->default('JP')->comment('言語');
        });
    }
};
