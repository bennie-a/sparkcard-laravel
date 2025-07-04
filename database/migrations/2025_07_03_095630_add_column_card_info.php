<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('card_info', function (Blueprint $table) {
            $table->integer('promotype_id')->nullable()->comment('プロモタイプID');
            $table->dropColumn('standard');
            $table->dropColumn('pioneer');
            $table->dropColumn('modarn');
            $table->dropColumn('legacy');
            $table->dropColumn('vintage');
            $table->dropColumn('commander');
            $table->dropColumn('pauper');
            // // 外部キー制約
            // $table->foreign('foiltype_id')->references('id')->on('foiltype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card_info', function (Blueprint $table) {    
            $table->dropColumn('promotype_id');
        });    }
};
