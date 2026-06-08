<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('target_date')->index(); // 日付検索を高速化
            $table->string('title');
            $table->unsignedTinyInteger('status')->default(0)->comment('0:進行中, 1:完了,');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_goals');
    }
};