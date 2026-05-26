<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{

    protected $fillable = [
        'user_id', 'category_id', 'content', 
        'x_position', 'y_position', 'is_starred', 'is_public'
    ];

    const CAT_SUCCESS = 1;  // 成功・良かったこと
    const CAT_LEARNING = 2; // 学び・改善
    const CAT_ACTION = 3;   // 明日やること

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
