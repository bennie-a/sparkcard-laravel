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
            $table->integer('base_id')->nullable()->after('quantity')->comment('BASE_ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stockpile', function (Blueprint $table) {
            $table->dropColumn('base_id');
        });
    }
};
