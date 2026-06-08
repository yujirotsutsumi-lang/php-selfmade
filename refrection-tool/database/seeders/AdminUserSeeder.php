<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // 検索条件：このメアドを探す
            [
                'name' => 'システム管理者',
                'password' => Hash::make('password'), // 🔑 パスワードは分かりやすく「password」
                'is_admin' => true,                   // 👑 【最重要】管理者フラグをON！
                'email_verified_at' => now(),         // 💡 メール認証済みにする（これがないと弾かれる場合があります）
            ]
        );
    }
}