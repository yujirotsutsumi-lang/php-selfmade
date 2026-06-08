<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 公開・非公開設定（デフォルトは非公開=false）
            $table->boolean('is_public')->default(false);
            // 頻出タグの保存用（JSON形式）
            $table->json('frequent_tags')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'frequent_tags']);
        });
    }
};
