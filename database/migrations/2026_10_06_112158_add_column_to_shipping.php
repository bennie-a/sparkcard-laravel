<?php

use App\Models\Shipping;
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
        Schema::table('shipping', function (Blueprint $table) {
            $table->boolean('deleted')->notNull()->default(false)->comment('削除フラグ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping', function (Blueprint $table) {
            $table->dropColumn('deleted');
        });
    }
};
