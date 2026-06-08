{{-- 📄 resources/views/livewire/admin/admin-user-reflections.blade.php --}}
<div class="py-6 min-h-screen bg-gray-50" wire:poll.5s> {{-- ⚡ 5秒ごとにデータベースを自動で見に行く魔法 --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- 🔙 管理者トップ（ダッシュボード）に戻るボタン --}}
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-[#b98c5c] transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 m0 0l7-7 m-7 7h18"/></svg>
                ユーザー一覧に戻る
            </a>
        </div>

        {{-- メインパネル --}}
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden flex min-h-[600px] border border-gray-100">
            
            {{-- 🟢 左側：カレンダーパネル (3/5) --}}
            <div class="w-3/5 p-8 border-r border-gray-100 flex flex-col">
                <div class="flex items-center justify-between mb-8 flex-shrink-0">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-xl font-black text-gray-700 mt-1">{{ $user->name }} さんのカレンダー</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button wire:click="previousMonth" class="p-2 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <h2 class="text-3xl font-black text-orange-500 tracking-tighter w-32 text-center">
                            {{ \Carbon\Carbon::parse($currentMonth)->format('Y.m') }}
                        </h2>
                        <button wire:click="nextMonth" class="p-2 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <button wire:click="goToCurrentMonth" class="px-4 py-2 text-xs font-bold text-orange-500 bg-orange-100 rounded-full hover:bg-orange-200 transition-colors shadow-sm">
                        今月
                    </button>
                </div>

                {{-- 曜日ヘッダー --}}
                <div class="grid grid-cols-7 gap-3 mb-4 flex-shrink-0">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="text-center text-xs font-bold text-gray-300 uppercase pb-4">{{ $day }}</div>
                    @endforeach
                </div>

                {{-- 日付グリッド --}}
                <div class="grid grid-cols-7 gap-3 flex-grow transition-opacity duration-300" 
                     wire:loading.class="opacity-40"
                     wire:target="previousMonth, nextMonth, goToCurrentMonth, selectDate">
                    @php
                        $startDayOfWeek = \Carbon\Carbon::parse($currentMonth)->startOfMonth()->dayOfWeek;
                        $daysInMonth = \Carbon\Carbon::parse($currentMonth)->daysInMonth;
                        $monthYear = \Carbon\Carbon::parse($currentMonth)->format('Y-m-');
                    @endphp

                    @for ($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="h-16"></div>
                    @endfor

                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php 
                            $date = $monthYear . sprintf('%02d', $day);
                            $isFuture = \Carbon\Carbon::parse($date)->isFuture();
                            $level = $this->getColorLevel($date);
                            $hasStar = $this->hasStarredNote($date);
                            $isAchieved = in_array($date, $achievedDates);
                            
                            $bgClass = match($level) {
                                0 => 'bg-gray-50 text-gray-400 hover:bg-gray-100',
                                1 => 'bg-emerald-50 text-emerald-900',
                                2 => 'bg-emerald-100 text-emerald-900',
                                3 => 'bg-emerald-200 text-emerald-950',
                                4 => 'bg-emerald-300 text-emerald-950',
                                5 => 'bg-emerald-400 text-emerald-950 shadow-inner',
                                6 => 'bg-emerald-500 text-white shadow-inner',
                                7 => 'bg-emerald-600 text-white shadow-inner',
                                8 => 'bg-emerald-700 text-white shadow-inner',
                                9 => 'bg-emerald-800 text-white shadow-inner font-black',
                                default => 'bg-gray-50 text-gray-400',
                            };
                            $isSelected = ($selectedDate === $date) ? 'ring-4 ring-orange-200 border-2 border-orange-400' : '';
                        @endphp

                        <button wire:click="selectDate('{{ $date }}')" @if($isFuture) disabled @endif
                                class="relative h-16 flex flex-col items-center justify-center rounded-2xl transition-all duration-200 {{ $bgClass }} {{ $isSelected }} {{ $isFuture ? 'opacity-30 cursor-not-allowed' : 'hover:scale-105' }}">
                                @if($isAchieved)
    <span class="absolute top-1 right-2 pointer-events-none" title="目標達成！">
        <svg class="w-[14px] h-[14px] text-[#eaff00] drop-shadow-[0_0_4px_rgba(234,255,0,0.9)]" fill="currentColor" viewBox="0 0 512 512">
            <path d="M226.5 92.9c14.3 42.9-.3 86.2-32.6 96.8s-70.3-15.6-84.6-58.5S110 45 142.3 34.4 212.2 50 226.5 92.9zm194.2 5.5c-15.2-42.6-56.1-59.5-91.2-37.7s-50.5 74.2-35.3 116.8 56.1 59.5 91.2 37.7 50.5-74.2 35.3-116.8zm-314.3 162c-35-21.8-76-4.9-91.2 37.7S30.7 415 65.8 436.8 141.8 441.7 157 399.1 141.4 282.2 106.4 260.4zm339 12.2c-18.4-41-62.1-53.7-97.4-28.5s-48.4 79-30 120 62.1 53.7 97.4 28.5 48.4-79 30-120zm-170.8-31.5c-41-11.4-86.5-1.9-114.7 24-27.1 24.8-37.7 60.1-27.5 94.7 11 37.1 45.4 62.2 83.3 64.9h2.3c4.1 0 8.3-.3 12.4-1 16-2.6 30.6-11.6 40.5-24.6 15.6-20.4 42-26.6 64-15 11.2 5.9 23.8 8.6 36.3 7.8 33.7-2 62.9-22 75.3-51.5 12-28.5 6.2-60.8-15.1-83.8-27-22-68.5-31-105.7-20.5-14.7 4.1-30.4 4.3-45.1 0-5.5-1.6-10.9-3.4-16-5.5z"/>
        </svg>
    </span>
@endif

                            <span class="text-lg font-bold">{{ $day }}</span>
                            
                            @if($hasStar)
                                <span class="absolute bottom-1.5 left-1/2 transform -translate-x-1/2 text-yellow-400 text-[10px] drop-shadow-sm">★</span>
                            @endif
                        </button>
                    @endfor
                </div>
            </div>

            {{-- 🟠 右側：詳細表示パネル (2/5) 【管理者向けに完全閲覧専用にカスタム】 --}}
            <div class="w-2/5 p-10 bg-gradient-to-br from-orange-50 to-white flex flex-col justify-between">
                <div>
                    <div class="mb-8">
                        <h3 class="text-[10px] font-black text-orange-300 uppercase tracking-[0.2em] mb-2">Selected Date</h3>
                        <p class="text-4xl font-black text-gray-800">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('n/j') }}
                            <span class="text-xl font-bold text-orange-400">
                                ({{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($selectedDate)->dayOfWeek] }})
                            </span>
                        </p>
                    </div>

                    <div class="space-y-6 overflow-y-auto max-h-[550px] pr-2">
                        <div class="flex items-center py-2 px-4 bg-gray-200/60 rounded-full w-max">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">ユーザーログ（閲覧専用）</span>
                        </div>

                        {{-- 当時の目標表示カード --}}
                        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm">
                            <h4 class="text-[10px] font-black text-gray-300 uppercase mb-1">設定していた目標</h4>
                            <p class="text-gray-700 font-bold leading-relaxed">{{ $selectedGoalTitle ?? '目標の記録なし' }}</p>
                        </div>

                        {{-- 3カテゴリの付箋データ表示 --}}
                        <div class="space-y-4">
                            @php
                                $categories = [
                                    1 => ['label' => '成功・良かったこと', 'color' => 'bg-pink-50 text-pink-600 border-pink-100'],
                                    2 => ['label' => '学び・改善点', 'color' => 'bg-green-50 text-green-600 border-green-100'],
                                    3 => ['label' => '明日へのアクション', 'color' => 'bg-blue-50 text-blue-600 border-blue-100'],
                                ];
                            @endphp

                            @foreach($categories as $id => $info)
                                <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center mb-3">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $info['color'] }}">
                                            {{ $info['label'] }}
                                        </span>
                                    </div>
                                    <ul class="space-y-2">
                                        @forelse($notes->where('category_id', $id) as $note)
                                            <li class="text-sm text-gray-600 flex items-start">
                                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-orange-300 mr-3 flex-shrink-0"></span>
                                                <span class="break-all">{{ $note->content }}</span>
                                                @if($note->is_starred)
                                                    <span class="ml-2 text-yellow-400 text-xs flex-shrink-0">★</span>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="text-xs text-gray-300 italic pl-4">付箋データがありません</li>
                                        @endforelse
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="text-center text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-6 flex-shrink-0">
                    Peta-Refle Monitoring System
                </div>
            </div>

        </div>
    </div>
</div>
