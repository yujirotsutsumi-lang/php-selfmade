{{-- 📄 resources/views/livewire/admin/admin-dashboard.blade.php --}}
<div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
    
    {{-- 管理者用オリジナルヘッダー --}}
    <div class="flex justify-between items-end mb-8 border-b border-gray-200 pb-4 px-4 sm:px-0">
        
        {{-- 🎯 左側：ロゴとタイトル --}}
        <div class="flex items-center gap-4">
            <x-application-logo class="w-20 h-auto mb-2" />
            <div>
                <h2 class="text-2xl font-black italic text-gray-800 tracking-tight">
                     <span class="text-sm font-bold text-gray-400 ml-2">Admin</span>
                </h2>
                <p class="text-sm font-bold text-[#b98c5c] mt-1">システム管理者ダッシュボード - ユーザー一覧</p>
            </div>
        </div>
        
        {{-- 🎯 右側：ログアウトボタン --}}
        <a href="{{ route('admin.login') }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 transition mb-1">
            ログアウト
        </a>
    </div>

    {{-- ユーザー一覧テーブル --}}
    <div class="bg-white overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-black tracking-wider">ユーザー名</th>
                        <th scope="col" class="px-6 py-4 font-black tracking-wider">メールアドレス</th>
                        <th scope="col" class="px-6 py-4 font-black tracking-wider">最終更新日</th> {{-- 🎯 ここを変更 --}}
                        <th scope="col" class="px-6 py-4 font-black tracking-wider text-center">アクション</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            
                            {{-- ユーザー名と公開タグ --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <span class="font-black text-gray-700">{{ $user->name }}</span>
                                    
                                    @if($user->is_public)
                                        <span class="px-2 py-0.5 text-[10px] font-black bg-orange-100 text-orange-500 border border-orange-200 rounded-full tracking-wider shadow-sm">
                                            公開中
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-black bg-gray-100 text-gray-400 border border-gray-200 rounded-full tracking-wider shadow-sm">
                                            非公開
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 font-medium text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-500">
                                {{-- 🎯 created_at から updated_at（最終更新日）に変更 --}}
                                {{ $user->updated_at->format('Y/m/d') }}
                            </td>
                            
                            {{-- アクション（公開状態によってボタンを出し分け） --}}
                            <td class="px-6 py-4 text-center">
                                @if($user->is_public)
                                    <a href="{{ route('admin.user.reflections', $user) }}" 
                                       wire:navigate
                                       class="inline-flex items-center px-4 py-2 bg-white border border-[#b98c5c] text-[#b98c5c] rounded-lg font-bold text-xs hover:bg-[#b98c5c] hover:text-white transition-colors active:scale-95 shadow-sm">
                                        振り返りを見る
                                    </a>
                                @else
                                    <button disabled 
                                            class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 text-gray-400 rounded-lg font-bold text-xs cursor-not-allowed opacity-70">
                                        閲覧不可
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- 🎯 カラムが4つになったので colspan="4" に修正 --}}
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold">
                                まだ一般ユーザーが登録されていません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- ページネーション --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>