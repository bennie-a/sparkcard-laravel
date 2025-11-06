<?php

use App\Enums\CsvType;
use App\Enums\ShopPlatform;
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
        Schema::table('csv_header', function (Blueprint $table) {
            $table->dropColumn('shop');
            $table->enum('shop', ['mercari', 'base'])
                ->notNull()
                ->comment('取り扱いショップ：mercari/base');
                $table->enum('csv_type', ['arrival', 'shipt'])
                    ->comment('arrival=入荷CSV / shipt=出荷CSV')
                    ->notNull();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('csv_header', function (Blueprint $table) {
            $table->dropColumn('csv_type');
        });

        Schema::table('csv_header', function (Blueprint $table) {
            $table->string('shop', 10)->notNull();
        });
    }
};
