{{-- resources/views/livewire/account-settings.blade.php --}}
<div class="py-12 bg-[#f9fafb] min-h-screen">
    <div class="max-w-3xl mx-auto px-4 space-y-8">
        
        <h2 class="text-3xl font-black text-gray-700 mb-8 border-l-4 border-[#b98c5c] pl-4">
            アカウント設定
        </h2>

        {{-- 1. プロフィール設定 --}}
        <div class="p-8 bg-white rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#b98c5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                基本プロフィール
            </h3>
            @if (session()->has('profile_message'))
                <div class="mb-4 p-3 bg-green-50 text-green-700 font-bold rounded-lg text-sm">{{ session('profile_message') }}</div>
            @endif
            <form wire:submit.prevent="saveProfile" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-2">ユーザー名</label>
                        <input type="text" wire:model="name" class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] py-3 px-4 font-medium text-gray-700">
                        @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="px-8 py-3 bg-[#b98c5c] hover:bg-[#a37a4e] text-white font-bold rounded-xl shadow transition">プロフィールを更新</button>
                </div>
            </form>
        </div>

        {{-- 2. アプリ設定（公開/非公開・頻出タグ） --}}
        <div class="p-8 bg-white rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#b98c5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                アプリのカスタマイズ
            </h3>
            @if (session()->has('preferences_message'))
                <div class="mb-4 p-3 bg-green-50 text-green-700 font-bold rounded-lg text-sm">{{ session('preferences_message') }}</div>
            @endif
            <form wire:submit.prevent="savePreferences" class="space-y-8">
                
                {{-- 公開・非公開トグル --}}
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <p class="font-bold text-gray-700">振り返りの公開設定</p>
                        <p class="text-sm text-gray-500 mt-1">他のユーザーにあなたの振り返りを公開するかどうかを設定します。</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_public" class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#b98c5c]"></div>
                        <span class="ml-3 text-sm font-bold text-gray-700">{{ $is_public ? '公開中' : '非公開' }}</span>
                    </label>
                </div>

                {{-- 頻出タグの編集 --}}
                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2">頻出タグの作成（カンマ「,」区切りで入力）</label>
                    <input type="text" wire:model="frequent_tags" placeholder="例: 重要, 継続, アイデア" class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] py-3 px-4 font-medium text-gray-700">
                    <p class="text-xs text-gray-400 mt-2">※キャンバス画面でメモを入力する際に、ここで設定したタグがボタンとして表示されます。</p>
                </div>

                <div class="text-right">
                    <button type="submit" class="px-8 py-3 bg-[#b98c5c] hover:bg-[#a37a4e] text-white font-bold rounded-xl shadow transition">設定を保存</button>
                </div>
            </form>
        </div>

        {{-- 3. パスワード変更 --}}
        <div class="p-8 bg-white rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#b98c5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                パスワードの変更
            </h3>
            @if (session()->has('password_message'))
                <div class="mb-4 p-3 bg-green-50 text-green-700 font-bold rounded-lg text-sm">{{ session('password_message') }}</div>
            @endif
            <form wire:submit.prevent="savePassword" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2">現在のパスワード</label>
                    <input type="password" wire:model="current_password" class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] py-3 px-4 font-medium text-gray-700">
                    @error('current_password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-2">新しいパスワード</label>
                        <input type="password" wire:model="new_password" class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] py-3 px-4 font-medium text-gray-700">
                        @error('new_password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-2">新しいパスワード（確認用）</label>
                        <input type="password" wire:model="new_password_confirmation" class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] py-3 px-4 font-medium text-gray-700">
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="px-8 py-3 bg-gray-800 hover:bg-black text-white font-bold rounded-xl shadow transition">パスワードを変更</button>
                </div>
            </form>
        </div>

        {{-- 🚀 4. アカウント削除（Danger Zone） --}}
        <div class="p-8 bg-red-50 rounded-2xl shadow-sm border border-red-100">
            <h3 class="text-xl font-bold text-red-600 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                アカウントの削除（退会）
            </h3>
            <p class="text-sm font-bold text-red-400 mb-6 leading-relaxed">
                アカウントを削除すると、これまでの振り返りデータ（目標・付箋など）がすべて完全に消去され、復元することはできなくなります。<br>
                本当に退会してもよろしいですか？
            </p>
            <div class="text-right">
                <button 
                    wire:click="deleteAccount"
                    wire:confirm="【⚠️警告】本当にアカウントを削除しますか？\n※この操作は取り消せません。これまでの振り返りデータがすべて消去されます。"
                    class="px-8 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow transition flex items-center justify-center ml-auto"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    アカウントを完全に削除する
                </button>
            </div>
        </div>

    </div>
</div>