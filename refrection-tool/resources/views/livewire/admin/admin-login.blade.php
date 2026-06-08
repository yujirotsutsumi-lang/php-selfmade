<div>
    {{-- 🎯 上部のタイトルエリア --}}
    <div class="text-center mb-6">
        <h2 class="text-xl font-black text-gray-700">Admin mode</h2>
        <p class="text-xs font-bold text-gray-400 mt-1">システム管理エリアへようこそ</p>
    </div>

    {{-- フォームエリア --}}
    <form wire:submit="login" class="space-y-6">
        <div>
            <label for="email" class="block text-sm font-bold text-gray-500 mb-2">管理者メールアドレス</label>
            <input wire:model="email" id="email" type="email" required autofocus 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            @error('email')
                <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-gray-500 mb-2">パスワード</label>
            <input wire:model="password" id="password" type="password" required 
                   class="w-full border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#b98c5c] focus:border-[#b98c5c] py-3 px-4 font-medium text-gray-700 transition-colors">
            @error('password')
                <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4">
            <button type="submit" 
                    class="w-full py-4 bg-[#b98c5c] hover:bg-[#a37a4e] text-white font-black text-lg rounded-xl shadow-lg shadow-[#b98c5c]/30 transition transform hover:-translate-y-1 active:scale-95 flex justify-center items-center">
                管理者としてログイン
            </button>
        </div>
    </form>
</div>