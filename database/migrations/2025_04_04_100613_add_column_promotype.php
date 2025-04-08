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
        Schema::table('promotype', function (Blueprint $table) {
            $table->string('exp_id')->default('c8f3a2b1-74e9-4d2f-b3a7-9e0f1c5d6a3b');
            // カラムの外部キー制約追加
            $table->foreign('exp_id')->references('notion_id')->on('expansion')->OnDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotype', function (Blueprint $table) {
            // 外部キー制約の削除
            $table->dropForeign('expantion_exp_id_foreign');
            $table->dropColumn('exp_id');
        });
    }
};
