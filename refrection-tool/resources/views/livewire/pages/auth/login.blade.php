<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('date_select', absolute: false), navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-black text-gray-700">おかえりなさい！</h2>
        <p class="text-sm font-bold text-gray-400 mt-2">今日の振り返りを始めましょう</p>
    </div>

    <form wire:submit="login" class="space-y-6">
        <div>
            <label for="email" class="block text-sm font-bold text-gray-500 mb-2">メールアドレス</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-xs font-bold" />
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-gray-500 mb-2">パスワード</label>
            <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password" 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-xs font-bold" />
        </div>

        <div class="flex items-center mt-4">
            <label for="remember" class="inline-flex items-center cursor-pointer group">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-[#b98c5c] shadow-sm focus:ring-[#b98c5c]">
                <span class="ms-2 text-sm font-bold text-gray-500 group-hover:text-gray-700 transition">ログイン状態を保持する</span>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-[#b98c5c] hover:bg-[#a37a4e] text-white font-black text-lg rounded-xl shadow-lg shadow-[#b98c5c]/30 transition transform hover:-translate-y-1 active:scale-95 flex justify-center items-center">
                ログインして始める
            </button>
        </div>
        
        @if (Route::has('register'))
        <div class="text-center mt-8 pt-6 border-t border-gray-100">
            <p class="text-sm text-gray-500 font-bold">
                アカウントをお持ちでないですか？ 
                <a href="{{ route('register') }}" class="text-[#b98c5c] hover:underline ml-1" wire:navigate>新規登録はこちら</a>
            </p>
        </div>
        @endif
    </form>
</div>