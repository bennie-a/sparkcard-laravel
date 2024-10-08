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
        Schema::table('stockpile', function (Blueprint $table) {
            $table->string("base_id")->comment("BASEの商品ID")->nullable();
            $table->string("mercari_id")->comment("メルカリの商品ID")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stockpile', function (Blueprint $table) {
            //
        });
    }
};
