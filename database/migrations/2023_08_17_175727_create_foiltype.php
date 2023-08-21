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
        Schema::create('foiltype', function (Blueprint $table) {
            $table->id();
            $table->string('attr', 20)->nullable(false)->comment('略称');
            $table->string('name', 20)->nullable(false)->comment('加工名');
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
        Schema::dropIfExists('foiltype');
    }
};
