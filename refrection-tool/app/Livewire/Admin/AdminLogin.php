<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminLogin extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::guard('admin')->attempt(['email' => $this->email, 'password' => $this->password, 'is_admin' => true])) {
            
            session()->regenerate();
            return redirect()->route('admin.dashboard');
        }
        // 一般ユーザーがここから入ろうとしたり、パスワードが違う場合はエラー
        $this->addError('email', '管理者権限を持つユーザーが見つからないか、パスワードが違います。');
    }

    public function render()
    {
        return view('livewire.admin.admin-login')
            ->layout('layouts.guest'); // 普段使っている共通レイアウトを適用
    }
}