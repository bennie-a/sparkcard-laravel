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
        //
        Schema::create('expansion', function(Blueprint $table) {
            $table->string('notion_id', 32)->primary()->comment('NotionのID');
            $table->integer('base_id')->nullable(false)->comment('BASEのID');
            $table->string('name', 60)->nullable(false)->comment('エキスパンション名');
            $table->string('attr', 7)->nullable(false)->comment('略称');
            $table->date('release_date')->nullable(false)->comment('発売日');

            $table->unique(['base_id', 'name', 'attr']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expansion');
    }
};
