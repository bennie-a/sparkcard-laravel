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
        Schema::create('exclude_promo', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('attr', 20)->nullable(false)->unique(true)->comment('略称');
            $table->string('name', 60)->nullable(false)->comment('プロモタイプ名');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exclude_promo');
    }
};
