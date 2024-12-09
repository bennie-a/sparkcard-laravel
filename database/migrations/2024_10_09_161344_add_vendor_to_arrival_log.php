<?php

use App\Models\VendorType;
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
            // カラム追加
            $table->integer('vendor_type_id')->unsigned()->after('id')->default(1);
            $table->string('vendor')->nullable(true)->comment("仕入先名")->after('vendor_type_id');
            // カラムの外部キー制約追加
            $table->foreign('vendor_type_id')->references('id')->on('vendor_type')->OnDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_log', function (Blueprint $table) {
            $table->dropColumn('vendor');
            // 外部キー制約の削除
            $table->dropForeign('arrival_log_vendor_type_id_foreign');
            // カラムの削除
            $table->dropColumn('vendor_type_id');
        });
    }
};
