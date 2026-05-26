<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // カテゴリ: 1:成功, 2:学び, 3:行動
            $table->unsignedTinyInteger('category_id')->comment('1:成功, 2:学び, 3:行動');
            $table->text('content');
            // 付箋の座標（レスポンシブを考慮し、親要素に対するパーセント等の小数を許容）
            $table->float('x_position')->default(0);
            $table->float('y_position')->default(0);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};