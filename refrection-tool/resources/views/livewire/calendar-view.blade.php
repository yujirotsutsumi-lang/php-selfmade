{{-- resources/views/livewire/calendar-view.blade.php --}}
<div class="py-6 min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden flex min-h-[600px] border border-gray-100">
            
            {{-- 左側：カレンダーパネル (3/5) --}}
            <div class="w-3/5 p-8 border-r border-gray-100 flex flex-col">
                
                <div class="flex items-center justify-between mb-8 flex-shrink-0">
                    <div class="flex items-center space-x-2">
                        {{-- 前の月ボタン --}}
                        <button wire:click="previousMonth" 
                                wire:loading.attr="disabled" 
                                class="p-2 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        
                        {{-- 年月表示 --}}
                        <h2 class="text-3xl font-black text-orange-500 tracking-tighter w-32 text-center">
                            {{ \Carbon\Carbon::parse($currentMonth)->format('Y.m') }}
                        </h2>
                        
                        {{-- 次の月ボタン --}}
                        <button wire:click="nextMonth" 
                                wire:loading.attr="disabled" 
                                class="p-2 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    {{-- 今月に戻るボタン --}}
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

                {{-- 日付グリッド（エメラルドヒートマップ） --}}
                <div class="grid grid-cols-7 gap-3 flex-grow transition-opacity duration-300" 
                    wire:loading.class="opacity-40">
                    @php
                        $startDayOfWeek = \Carbon\Carbon::parse($currentMonth)->startOfMonth()->dayOfWeek;
                        $daysInMonth = \Carbon\Carbon::parse($currentMonth)->daysInMonth;
                        $monthYear = \Carbon\Carbon::parse($currentMonth)->format('Y-m-');
                    @endphp

                    {{-- 1. 開始日までの空白埋め --}}
                    @for ($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="h-16"></div>
                    @endfor

                    {{-- 2. 日付ボタンループ --}}
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php 
                            $date = $monthYear . sprintf('%02d', $day);
                            $isFuture = \Carbon\Carbon::parse($date)->isFuture();
                            $level = $this->getColorLevel($date);

                            $hasStar = $this->hasStarredNote($date);
                            
                            // 🚀 目標達成日チェック
                            $isAchieved = in_array($date, $this->achievedDates);
                            
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

                        <button 
                            wire:click="selectDate('{{ $date }}')"
                            @if($isFuture) disabled @endif
                            class="relative h-16 flex flex-col items-center justify-center rounded-2xl transition-all duration-200 {{ $bgClass }} {{ $isSelected }} {{ $isFuture ? 'opacity-30 cursor-not-allowed' : 'hover:scale-105' }}"
                        >
                            {{-- 目標達成マーク（✓） --}}
                            @if($isAchieved)
                                <span class="absolute top-1 right-2 text-yellow-300 font-extrabold text-[13px] drop-shadow-sm select-none">
                                    ✓
                                </span>
                            @endif

                            <span class="text-lg font-bold">{{ $day }}</span>
                            
                            @if($hasStar)
                                <span class="absolute bottom-1.5 left-1/2 transform -translate-x-1/2 text-yellow-400 text-[10px] drop-shadow-sm">
                                    ★
                                </span>
                            @endif
                        </button>
                    @endfor
                </div>
            </div>

            {{-- 右側：詳細・目標パネル (2/5) --}}
            <div class="w-2/5 p-10 bg-gradient-to-br from-orange-50 to-white flex flex-col justify-between max-h-[600px]">
                <div class="flex flex-col h-full overflow-hidden">
                    <div class="mb-6 flex-shrink-0">
                        <h3 class="text-[10px] font-black text-orange-300 uppercase tracking-[0.2em] mb-2">Selected Date</h3>
                        <p class="text-4xl font-black text-gray-800">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('n/j') }}
                            <span class="text-xl font-bold text-orange-400">
                                ({{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($selectedDate)->dayOfWeek] }})
                            </span>
                        </p>
                    </div>

                    @php
                        // カテゴリー定義（今日でも過去でも使い回せるようにここで定義）
                        $categories = [
                            1 => ['label' => '成功・良かったこと', 'color' => 'bg-pink-50 text-pink-600 border-pink-100'],
                            2 => ['label' => '学び・改善点', 'color' => 'bg-green-50 text-green-600 border-green-100'],
                            3 => ['label' => '明日へのアクション', 'color' => 'bg-blue-50 text-blue-600 border-blue-100'],
                        ];
                    @endphp

                    @if($isToday)
                        {{-- ========================================== --}}
                        {{-- 🌟 今日用の表示エリア --}}
                        {{-- ========================================== --}}
                        
                        {{-- パターンA：目標未設定 or 編集モード --}}
                        @if(!$goal || $isEditingGoal)
                            <div class="p-6 bg-white rounded-3xl shadow-sm border border-orange-300 ring-4 ring-orange-50 flex-shrink-0">
                                <h4 class="text-xs font-bold text-orange-400 mb-3 uppercase">Set Today's Goal</h4>
                                <input type="text" wire:model="newGoalTitle" placeholder="今日の目標を入力してください..." 
                                       class="w-full border-gray-200 rounded-xl shadow-inner focus:ring-orange-400 focus:border-orange-400 mb-2 p-3 text-sm font-bold text-gray-700">
                                @error('newGoalTitle') 
                                    <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> 
                                @enderror
                                <div class="flex gap-2 mt-4">
                                    <button wire:click="saveGoal" class="flex-1 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-all shadow-md text-sm">
                                        保存する
                                    </button>
                                    @if($isEditingGoal)
                                        <button wire:click="$set('isEditingGoal', false)" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold rounded-xl transition-all text-sm">
                                            キャンセル
                                        </button>
                                    @endif
                                </div>
                            </div>

                        {{-- パターンB：目標設定済み（過去ログ風のサマリー画面） --}}
                        @else
                            <div class="flex-grow overflow-y-auto pr-2 space-y-4">
                                {{-- 今日の目標カード --}}
                                <div class="p-5 bg-white rounded-2xl border border-orange-100 shadow-sm relative">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-[10px] font-black text-orange-400 uppercase">Today's Goal</h4>
                                        <button wire:click="editGoal" class="text-gray-400 hover:text-orange-500 transition-colors text-xs font-bold flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            編集
                                        </button>
                                    </div>
                                    <p class="text-xl font-bold text-gray-800 mb-4">{{ $goal->title }}</p>

                                    {{-- セレクトボックス --}}
                                    <select wire:model.live="goalStatus" class="w-full border-orange-200 bg-orange-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-400 py-2.5 px-3 text-sm font-bold text-orange-700 cursor-pointer outline-none">
                                        <option value="0">🏃‍♂️ 進行中（キャンバスで振り返る）</option>
                                        <option value="1">🐾 目標達成！お疲れ様でした！</option>
                                    </select>
                                </div>

                                {{-- 付箋のサマリー --}}
                                <div class="space-y-4 pb-4">
                                    @foreach($categories as $id => $info)
                                        <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                            <div class="flex items-center mb-3">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $info['color'] }}">
                                                    {{ $info['label'] }}
                                                </span>
                                            </div>
                                            <ul class="space-y-2">
                                                @forelse($notes->where('category_id', $id) as $note)
                                                    <li class="text-sm text-gray-600 flex items-start font-medium">
                                                        <span class="mt-2 w-1.5 h-1.5 rounded-full bg-orange-300 mr-3 flex-shrink-0"></span>
                                                        <span class="break-all">{{ $note->content }}</span>
                                                        @if($note->is_starred)
                                                            <span class="ml-2 text-yellow-400 text-xs flex-shrink-0">★</span>
                                                        @endif
                                                    </li>
                                                @empty
                                                    <li class="text-xs text-gray-300 italic pl-4 font-bold">まだ付箋がありません</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- キャンバスを開くボタン（下部に固定） --}}
                            <div class="mt-4 flex-shrink-0 pt-2 border-t border-orange-100">
                                <a href="{{ route('reflection', ['date' => $selectedDate]) }}" wire:navigate 
                                   class="block text-center w-full py-4 bg-pink-400 hover:bg-pink-500 text-white text-lg font-black rounded-3xl shadow-lg shadow-pink-100 transition-all transform hover:-translate-y-1 active:scale-95">
                                    キャンバスを開く
                                </a>
                            </div>
                        @endif

                    @else
                        {{-- ========================================== --}}
                        {{-- 💡 過去ログ用の表示エリア --}}
                        {{-- ========================================== --}}
                        <div class="flex items-center py-2 px-4 bg-gray-200/60 rounded-full w-max mb-4 flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">過去のログ（閲覧専用）</span>
                        </div>

                        <div class="flex-grow overflow-y-auto pr-2 space-y-4 pb-4">
                            {{-- 🎯 当時の目標カード --}}
                            <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                                <div>
                                    <h4 class="text-[10px] font-black text-gray-300 uppercase mb-1">当時の目標</h4>
                                    <p class="text-gray-700 font-bold leading-relaxed">{{ $goal->title ?? '目標の記録なし' }}</p>
                                </div>
                                {{-- 🎯 右側に当時の達成ステータスを表示 --}}
                                @if($goal)
                                    <div>
                                        @if($goal->status === 1)
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 text-[10px] font-black rounded-full border border-yellow-200">達成済 🐾</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black rounded-full border border-gray-200">未達成</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- 🎯 付箋のカテゴリー別表示 --}}
                            <div class="space-y-4">
                                @foreach($categories as $id => $info)
                                    <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                        <div class="flex items-center mb-3">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $info['color'] }}">
                                                {{ $info['label'] }}
                                            </span>
                                        </div>
                                        <ul class="space-y-2">
                                            @forelse($notes->where('category_id', $id) as $note)
                                                <li class="text-sm text-gray-600 flex items-start font-medium">
                                                    <span class="mt-2 w-1.5 h-1.5 rounded-full bg-orange-300 mr-3 flex-shrink-0"></span>
                                                    <span class="break-all">{{ $note->content }}</span>
                                                    @if($note->is_starred)
                                                        <span class="ml-2 text-yellow-400 text-xs flex-shrink-0">★</span>
                                                    @endif
                                                </li>
                                            @empty
                                                <li class="text-xs text-gray-300 italic pl-4 font-bold">付箋データがありません</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- フッター（管理者リンクなど） --}}
                <div class="mt-6 flex-shrink-0 text-center">
                    <div class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mb-1">
                        Peta-Refle Dashboard
                    </div>
                    <a href="{{ route('admin.login') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="text-[10px] text-gray-400 hover:text-orange-400 font-bold underline transition-colors cursor-pointer">
                        管理者ログインはこちら
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>