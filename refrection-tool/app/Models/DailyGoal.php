<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyGoal extends Model
{
    protected $fillable = ['user_id', 'target_date', 'title', 'status'];

    const STATUS_INACTIVE = 0; // 未着手
    const STATUS_PROGRESS = 1; // 進行中
    const STATUS_COMPLETED = 2; // 完了

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
