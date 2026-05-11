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
        Schema::create('base_token', function (Blueprint $table) {
            $table->id();
            $table->text('access_token')->comment('アクセストークン')->notNull();
            $table->text('refresh_token')->comment('リフレッシュトークン')->notNull();
            $table->dateTime('expires_in')->comment('有効期限')->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('base_token');
    }
};
