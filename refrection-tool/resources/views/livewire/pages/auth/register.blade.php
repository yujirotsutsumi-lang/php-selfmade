<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        // 🚀 登録後もカレンダー画面（date_select）に飛ぶように統一しています
        $this->redirect(route('date_select', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-black text-gray-700">アカウントを作成 🐾</h2>
        <p class="text-sm font-bold text-gray-400 mt-2">Peta-Refleで日々の振り返りを始めましょう</p>
    </div>

    <form wire:submit="register" class="space-y-6">
        {{-- ユーザー名 --}}
        <div>
            <label for="name" class="block text-sm font-bold text-gray-500 mb-2">ユーザー名</label>
            <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs font-bold" />
        </div>

        {{-- メールアドレス --}}
        <div>
            <label for="email" class="block text-sm font-bold text-gray-500 mb-2">メールアドレス</label>
            <input wire:model="email" id="email" type="email" name="email" required autocomplete="username" 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold" />
        </div>

        {{-- パスワード --}}
        <div>
            <label for="password" class="block text-sm font-bold text-gray-500 mb-2">パスワード</label>
            <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password" 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-bold" />
        </div>

        {{-- パスワード（確認用） --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-gray-500 mb-2">パスワード（確認用）</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs font-bold" />
        </div>

        {{-- 登録ボタン --}}
        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-[#b98c5c] hover:bg-[#a37a4e] text-white font-black text-lg rounded-xl shadow-lg shadow-[#b98c5c]/30 transition transform hover:-translate-y-1 active:scale-95 flex justify-center items-center">
                登録して始める
            </button>
        </div>

        {{-- ログインへのリンク --}}
        @if (Route::has('login'))
        <div class="text-center mt-8 pt-6 border-t border-gray-100">
            <p class="text-sm text-gray-500 font-bold">
                すでにアカウントをお持ちですか？ 
                <a href="{{ route('login') }}" class="text-[#b98c5c] hover:underline ml-1" wire:navigate>ログインはこちら</a>
            </p>
        </div>
        @endif
    </form>
</div>