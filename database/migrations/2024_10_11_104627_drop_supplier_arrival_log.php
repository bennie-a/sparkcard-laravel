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
        Schema::table('arrival_log', function (Blueprint $table) {
            $table->dropUnique(['supplier', 'arrival_date', 'stock_id', 'quantity']);
            $table->dropColumn("supplier");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_log', function (Blueprint $table) {
            $table->string('supplier')->notNull();
        });
    }
};
