<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Note;      // 🚀 追加：付箋を削除するために読み込む
use App\Models\DailyGoal; // 🚀 追加：目標を削除するために読み込む

class AccountSettings extends Component
{
    // プロフィール用
    public $name;
    public $email;

    // パスワード用
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // 設定用
    public $is_public = false;
    public $frequent_tags = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_public = $user->is_public ?? false;

        // DBにタグがあればカンマ区切りの文字列にしてフォームに表示（無ければデフォルト値をセット）
        $tags = $user->frequent_tags ?? ['重要', '継続', '要確認', 'ひらめき'];
        $this->frequent_tags = is_array($tags) ? implode(', ', $tags) : $tags;
    }

    // ① プロフィールの保存
    public function saveProfile()
    {
        $user = Auth::user();
        $this->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        $user->name = $this->name;
        $user->save();

        session()->flash('profile_message', 'プロフィール情報を更新しました！');
    }

    // ② パスワードの変更
    public function savePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => '現在のパスワードが間違っています。',
            'new_password.confirmed' => '新しいパスワードが確認用と一致しません。',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('password_message', 'パスワードを安全に変更しました！');
    }

    // ③ アプリ設定（公開/非公開・頻出タグ）の保存
    public function savePreferences()
    {
        $user = Auth::user();

        // カンマ区切りの文字列を配列に変換し、前後の空白を削除
        $tagsArray = array_filter(array_map('trim', explode(',', $this->frequent_tags)));

        $user->is_public = $this->is_public;
        $user->frequent_tags = $tagsArray;
        $user->save();

        session()->flash('preferences_message', 'アプリ設定を更新しました！');
    }

    // 🚀 ④ アカウントの削除（退会処理）を追加
    public function deleteAccount()
    {
        $user = Auth::user();

        // 1. このユーザーが書いた付箋と目標をすべて削除（ゴミを残さない）
        Note::where('user_id', $user->id)->delete();
        DailyGoal::where('user_id', $user->id)->delete();

        // 2. ログアウト処理
        Auth::logout();

        // 3. ユーザー本体をデータベースから削除
        $user->delete();

        // 4. セッションを完全に破壊して無効化（セキュリティ対策）
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // 5. ログイン画面へ強制送還
        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.account-settings')
            ->layout('layouts.app', ['header' => 'アカウント設定']);
    }
}