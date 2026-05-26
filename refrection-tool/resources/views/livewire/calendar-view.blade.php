{{-- resources/views/livewire/calendar-view.blade.php --}}
{{-- ⚠️ 最初と最後にあった <x-app-layout> タグは削除したままでOK ⚠️ --}}

<div class="py-6 min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden flex min-h-[600px] border border-gray-100">
            
            <div class="w-3/5 p-8 border-r border-gray-100 flex flex-col">
                
                {{-- 👇 ここが変更点：矢印付きの月移動ナビゲーション --}}
                <div class="flex items-center justify-between mb-8 flex-shrink-0">
                    <div class="flex items-center space-x-2">
                        {{-- 前の月ボタン --}}
                        <button wire:click="previousMonth" class="p-2 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        
                        {{-- 年月表示 --}}
                        <h2 class="text-3xl font-black text-orange-500 tracking-tighter w-32 text-center">
                            {{ \Carbon\Carbon::parse($currentMonth)->format('Y.m') }}
                        </h2>
                        
                        {{-- 次の月ボタン --}}
                        <button wire:click="nextMonth" class="p-2 text-orange-400 hover:text-orange-600 hover:bg-orange-50 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    {{-- 今月に戻るボタン --}}
                    <button wire:click="goToCurrentMonth" class="px-4 py-2 text-xs font-bold text-orange-500 bg-orange-100 rounded-full hover:bg-orange-200 transition-colors shadow-sm">
                        今月
                    </button>
                </div>
                {{-- 👆 変更点ここまで --}}

                {{-- 曜日ヘッダー: gridで7列に並べる --}}
                <div class="grid grid-cols-7 gap-3 mb-4 flex-shrink-0">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="text-center text-xs font-bold text-gray-300 uppercase pb-4">{{ $day }}</div>
                    @endforeach
                </div>

                {{-- 日付グリッド: gridで7列 --}}
                <div class="grid grid-cols-7 gap-3 flex-grow">
                    {{-- カレンダー生成ロジック --}}
                    @php
                        $startDayOfWeek = \Carbon\Carbon::parse($currentMonth)->startOfMonth()->dayOfWeek;
                        $daysInMonth = \Carbon\Carbon::parse($currentMonth)->daysInMonth;
                        $monthYear = \Carbon\Carbon::parse($currentMonth)->format('Y-m-');
                    @endphp

                    {{-- 1. 月開始日までの空白埋め: gridの直系子要素 --}}
                    @for ($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="h-16"></div>
                    @endfor

                    {{-- 2. 実際の日付ボタンループ: gridの直系子要素 --}}
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php 
                            $date = $monthYear . sprintf('%02d', $day);
                            $isFuture = \Carbon\Carbon::parse($date)->isFuture();
                            $level = $this->getColorLevel($date);
                            
                            // 保存された付箋の量や重要度に応じた濃淡（0〜3）
                            $bgClass = match($level) {
                                0 => 'bg-gray-50 text-gray-400 hover:bg-gray-100',
                                1 => 'bg-orange-100 text-orange-600',
                                2 => 'bg-orange-300 text-white',
                                3 => 'bg-orange-500 text-white shadow-inner',
                            };
                            // 選択中の日付に太いオレンジの枠線をつける
                            $isSelected = ($selectedDate === $date) ? 'ring-4 ring-orange-200 border-2 border-orange-400' : '';
                        @endphp

                        <button 
                            wire:click="selectDate('{{ $date }}')"
                            @if($isFuture) disabled @endif
                            class="h-16 flex flex-col items-center justify-center rounded-2xl transition-all duration-200 {{ $bgClass }} {{ $isSelected }} {{ $isFuture ? 'opacity-30 cursor-not-allowed' : 'hover:scale-105' }}"
                        >
                            <span class="text-lg font-bold">{{ $day }}</span>
                        </button>
                    @endfor
                </div>
            </div>

            <div class="w-2/5 p-10 bg-gradient-to-br from-orange-50 to-white flex flex-col justify-between">
                <div>
                    <div class="mb-12">
                        <h3 class="text-[10px] font-black text-orange-300 uppercase tracking-[0.2em] mb-2">Selected Date</h3>
                        <p class="text-4xl font-black text-gray-800">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('n/j') }}
                            <span class="text-xl font-bold text-orange-400">
                                ({{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($selectedDate)->dayOfWeek] }})
                            </span>
                        </p>
                    </div>

                    @if($isToday)
                        <div class="space-y-6">
                            
                            @if($isEditingGoal)
                                {{-- 目標設定：編集モード（フォームの表示） --}}
                                <div class="p-6 bg-white rounded-3xl shadow-sm border border-orange-300 ring-4 ring-orange-50">
                                    <h4 class="text-xs font-bold text-orange-400 mb-3 uppercase">Set Today's Goal</h4>
                                    
                                    {{-- wire:model で入力テキストをPHP変数にリアルタイム同期 --}}
                                    <input type="text" wire:model="newGoalTitle" placeholder="今日の目標を入力してください..." 
                                           class="w-full border-gray-200 rounded-xl shadow-inner focus:ring-orange-400 focus:border-orange-400 mb-2 p-3 text-sm">
                                    
                                    @error('newGoalTitle') 
                                        <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> 
                                    @enderror

                                    <div class="flex gap-2 mt-4">
                                        <button wire:click="saveGoal" class="flex-1 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-all shadow-md text-sm">
                                            保存する
                                        </button>
                                        <button wire:click="$set('isEditingGoal', false)" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold rounded-xl transition-all text-sm">
                                            キャンセル
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- 目標設定：通常表示モード --}}
                                <div class="p-6 bg-white rounded-3xl shadow-sm border border-orange-100 relative group">
                                    <h4 class="text-xs font-bold text-orange-400 mb-3 uppercase">Today's Goal</h4>
                                    <p class="text-xl font-bold {{ $goal ? 'text-gray-800' : 'text-gray-300 italic' }}">
                                        {{ $goal ? $goal->title : '目標をセットしましょう' }}
                                    </p>
                                    
                                    {{-- 目標を変更するためのペンアイコンボタン --}}
                                    <button wire:click="editGoal" class="absolute top-6 right-6 text-gray-300 hover:text-orange-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </div>
                                
                                {{-- ボタンエリア：目標が無い時は設定へ誘導、ある時は振り返りボタンを有効化 --}}
                                <div class="space-y-3">
                                    @if(!$goal)
                                        <button wire:click="editGoal" class="w-full py-4 bg-orange-400 hover:bg-orange-500 text-white text-lg font-bold rounded-2xl transition-all shadow-lg shadow-orange-100">
                                            目標を設定する
                                        </button>
                                    @endif
                                    
                                    <a href="{{ $goal ? route('reflection', ['date' => $selectedDate]) : '#' }}" 
                                        class="block text-center w-full py-5 bg-pink-400 hover:bg-pink-500 text-white text-xl font-black rounded-3xl shadow-lg shadow-pink-100 transition-all transform hover:-translate-y-1 active:scale-95 {{ !$goal ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                                        振り返りを始める
                                    </a>
                                </div>
                            @endif
                        </div>

                    @else
                        <div class="space-y-6">
                            <div class="flex items-center py-2 px-4 bg-gray-200/60 rounded-full w-max mb-6">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">過去のログ（閲覧専用）</span>
                            </div>

                            {{-- 過去の目標表示 --}}
                            <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                <h4 class="text-[10px] font-black text-gray-300 uppercase mb-1">当時の目標</h4>
                                <p class="text-gray-700 font-bold leading-relaxed">{{ $goal->title ?? '目標の記録なし' }}</p>
                            </div>

                            {{-- 過去に貼られた付箋の一覧をカテゴリー別に出力 --}}
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
                    @endif
                </div>

                {{-- 右パネルの一番下につける控えめなアプリロゴ・注釈 --}}
                <div class="text-center text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-6">
                    Peta-Refle Dashboard
                </div>
            </div>

        </div>
    </div>
</div>