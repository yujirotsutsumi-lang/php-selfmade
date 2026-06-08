{{-- resources/views/livewire/reflection-canvas.blade.php --}}
<div class="h-screen overflow-hidden bg-[#f3f4f6] relative flex flex-col text-sm">
    
    {{-- ヘッダー・サブナビゲーション（高さをキュッと圧縮） --}}
    <div class="flex-none bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shadow-sm z-10">
        <div class="flex items-center space-x-4">
            <a href="{{ route('date_select') }}" 
               title="カレンダーに戻る"
               class="p-2 bg-gray-100 hover:bg-[#b98c5c] text-gray-500 hover:text-white rounded-lg shadow-sm transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </a>
        </div>

        {{-- 今日の目標表示エリア（コンパクト化） --}}
        <div class="flex-1 max-w-xl mx-8 p-2.5 bg-[#f9fafb] rounded border border-gray-200 text-center">
            <p class="text-[10px] font-bold text-gray-400 mb-0.5 uppercase tracking-widest">今日の目標</p>
            <p class="text-base font-bold transition-all duration-500 {{ $goalStatus == 1 ? 'text-gray-300 line-through' : 'text-gray-700' }}">
                {{ $goal->title ?? '目標が設定されていません' }}
            </p>
        </div>

        <div class="flex items-center space-x-6">
            <div class="text-right">
    <span class="text-[10px] text-gray-400 font-bold block uppercase mb-0.5">Status</span>
    
    {{-- 💡 矢印を絶対配置するために、セレクトボックスの親を relative（inline-block）にする --}}
    <div class="relative inline-block text-left">
        <select wire:model.live="goalStatus" 
                class="appearance-none bg-transparent border-none font-bold text-orange-500 focus:ring-0 pt-0 pb-0 pl-0 pr-4 text-sm cursor-pointer select-none">
            <option value="0">進行中</option>
            <option value="1">完了</option>
        </select>
    </div>
</div>
            
            <button wire:click="openModal" class="bg-white border border-gray-200 rounded-lg px-4 py-1 hover:bg-gray-50 transition shadow-sm flex items-center justify-center">
                <span class="text-3xl font-light text-gray-400 leading-none">+</span>
            </button>
        </div>
    </div>

    {{-- メインキャンバスエリア（残りの高さをすべて使う） --}}
    <div class="flex-1 p-4 overflow-hidden">
        <div class="grid grid-cols-3 gap-4 h-full">
            
            {{-- 枠1：成功・良かったこと --}}
            <div x-data="{ isHovered: false }"
            class="bg-[#e8f5e9] rounded-xl p-4 shadow-sm flex flex-col border border-[#c8e6c9] overflow-hidden h-full transition-all duration-200"
            :class="isHovered ? 'ring-4 ring-inset ring-green-600/20 brightness-95' : ''"
            x-on:dragover.prevent="isHovered = true"
            x-on:dragleave.prevent="isHovered = false"
            x-on:drop="isHovered = false; $wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 1)">
                <h3 class="font-bold text-gray-700 mb-3 text-sm flex items-center">成功・良かったこと</h3>
                <div class="flex-grow space-y-2 overflow-y-auto pb-8 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full pr-1">
                    @foreach($notes->where('category_id', 1) as $note)
                        <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                             class="p-3 bg-white shadow-sm rounded-lg border border-gray-100 text-xs font-medium cursor-pointer hover:shadow-md transition active:scale-95 relative pl-3 pr-6">
                            <div class="space-y-1">
                                @foreach(explode("\n", $note->content) as $line)
                                    @if(trim($line) !== '')
                                        <div class="flex items-start">
                                            <span class="mr-1.5 text-[#4caf50] font-bold">•</span>
                                            <span class="leading-relaxed">{{ $line }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if($note->is_starred)
                                <span class="absolute top-1.5 right-1.5 text-yellow-400 text-sm">★</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 枠0：未仕分け --}}
            <div x-data="{ isHovered: false }"
            class="bg-[#f5f5f5] rounded-xl p-4 shadow-sm flex flex-col border border-[#e0e0e0] overflow-hidden h-full transition-all duration-200"
            :class="isHovered ? 'ring-4 ring-inset ring-black/10 brightness-95' : ''"
            x-on:dragover.prevent="isHovered = true"
            x-on:dragleave.prevent="isHovered = false"
            x-on:drop="isHovered = false; $wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 0)">
                <h3 class="font-bold text-gray-700 mb-3 text-sm">未仕分け</h3>
                <div class="flex-grow space-y-2 overflow-y-auto pb-8 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full pr-1">
                    @foreach($notes->where('category_id', 0) as $note)
                        <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                             class="p-3 bg-white shadow-sm rounded-lg border border-gray-100 text-xs font-medium cursor-pointer hover:shadow-md transition active:scale-95 border-l-4 border-l-gray-400 relative pl-3 pr-6">
                            <div class="space-y-1">
                                @foreach(explode("\n", $note->content) as $line)
                                    @if(trim($line) !== '')
                                        <div class="flex items-start">
                                            <span class="mr-1.5 text-gray-400 font-bold">•</span>
                                            <span class="leading-relaxed">{{ $line }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if($note->is_starred)
                                <span class="absolute top-1.5 right-1.5 text-yellow-400 text-sm">★</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 右：学び と 明日やる --}}
            <div class="grid grid-rows-2 gap-4 h-full overflow-hidden">
                
                {{-- 枠2：学び・改善 --}}
                <div x-data="{ isHovered: false }"
                class="bg-[#fce4ec] rounded-xl p-4 shadow-sm flex flex-col border border-[#f8bbd0] overflow-hidden h-full transition-all duration-200"
                :class="isHovered ? 'ring-4 ring-inset ring-pink-600/20 brightness-95' : ''"
                x-on:dragover.prevent="isHovered = true"
                x-on:dragleave.prevent="isHovered = false"
                x-on:drop="isHovered = false; $wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 2)">
                    <h3 class="font-bold text-gray-700 mb-3 text-sm">学び・改善</h3>
                    <div class="flex-grow space-y-2 overflow-y-auto pb-8 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full pr-1">
                        @foreach($notes->where('category_id', 2) as $note)
                            <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                                 class="p-3 bg-white shadow-sm rounded-lg border border-gray-100 text-xs font-medium cursor-pointer hover:shadow-md transition active:scale-95 relative pl-3 pr-6">
                                <div class="space-y-1">
                                    @foreach(explode("\n", $note->content) as $line)
                                        @if(trim($line) !== '')
                                            <div class="flex items-start">
                                                <span class="mr-1.5 text-[#ef5350] font-bold">•</span>
                                                <span class="leading-relaxed">{{ $line }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @if($note->is_starred)
                                    <span class="absolute top-1.5 right-1.5 text-yellow-400 text-sm">★</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 枠3：明日やる --}}
                <div x-data="{ isHovered: false }"
                class="bg-[#e3f2fd] rounded-xl p-4 shadow-sm flex flex-col border border-[#bbdefb] overflow-hidden h-full transition-all duration-200"
                :class="isHovered ? 'ring-4 ring-inset ring-blue-600/20 brightness-95' : ''"
                x-on:dragover.prevent="isHovered = true"
                x-on:dragleave.prevent="isHovered = false"
                x-on:drop="isHovered = false; $wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 3)">
                    <h3 class="font-bold text-gray-700 mb-3 text-sm">明日やる</h3>
                    <div class="flex-grow space-y-2 overflow-y-auto pb-8 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full pr-1">
                        @foreach($notes->where('category_id', 3) as $note)
                            <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                                 class="p-3 bg-white shadow-sm rounded-lg border border-gray-100 text-xs font-medium cursor-pointer hover:shadow-md transition active:scale-95 relative pl-3 pr-6">
                                <div class="space-y-1">
                                    @foreach(explode("\n", $note->content) as $line)
                                        @if(trim($line) !== '')
                                            <div class="flex items-start">
                                                <span class="mr-1.5 text-blue-400 font-bold">•</span>
                                                <span class="leading-relaxed">{{ $line }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @if($note->is_starred)
                                    <span class="absolute top-1.5 right-1.5 text-yellow-400 text-sm">★</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ゴミ箱ドロップエリア（少し小さく・位置を調整） --}}
    <div x-data="{ isHovered: false }"
         x-on:dragover.prevent="isHovered = true"
         x-on:dragleave="isHovered = false"
         x-on:drop="isHovered = false; if(confirm('この付箋を削除してもよろしいですか？')) { $wire.deleteNote(event.dataTransfer.getData('noteId')) }"
         :class="isHovered ? 'bg-red-500 text-white scale-110 shadow-2xl' : 'bg-white text-red-400 border-2 border-red-100 shadow-md hover:bg-red-50'"
         class="fixed bottom-4 left-1/2 transform -translate-x-1/2 w-64 h-12 rounded-full flex items-center justify-center transition-all duration-300 z-40 opacity-90 hover:opacity-100">
        
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        <span class="text-xs font-bold tracking-widest">ここにドロップして削除</span>
    </div>

    {{-- 付箋作成/編集モーダル（変更なし） --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="closeModal" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

            <div class="relative bg-[#d1d5db] w-full max-w-md rounded-2xl shadow-2xl p-6 flex flex-col overflow-hidden">
                <h2 class="text-xl font-bold text-gray-700 text-center mb-4">
                    {{ $editingNoteId ? '付箋の編集' : '付箋の作成' }}
                </h2>

                <div class="bg-white rounded-lg p-3 shadow-inner mb-4 relative">
                    <textarea wire:model="noteContent" rows="4" placeholder="ここにメモ..." 
                              class="w-full border-none focus:ring-0 text-gray-600 text-sm font-medium resize-none"></textarea>
                    
                    <button wire:click="$toggle('isStarred')" class="absolute bottom-3 right-3 transition-transform active:scale-125">
                        <svg class="w-6 h-6 {{ $isStarred ? 'text-yellow-400 fill-current' : 'text-gray-300' }}" 
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <p class="text-xs font-bold text-gray-500 mb-2 text-center uppercase tracking-widest">頻出項目のタグを表示</p>
                    <div class="flex flex-wrap justify-center gap-1.5">
                        @foreach($frequentTags as $tag)
                            <button wire:click="addTag('{{ $tag }}')" 
                                    class="px-3 py-1 bg-gray-100 hover:bg-white text-gray-500 text-[10px] font-bold rounded-full border border-gray-200 transition">
                                #{{ $tag }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3">
                    <button wire:click="saveNote" class="flex-1 py-3 bg-[#66bb6a] hover:bg-[#4caf50] text-white text-sm font-black rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                        {{ $editingNoteId ? '更新する' : '作成' }}
                    </button>
                    <button wire:click="closeModal" class="flex-1 py-3 bg-[#ef5350] hover:bg-[#f44336] text-white text-sm font-black rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                        キャンセル
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>