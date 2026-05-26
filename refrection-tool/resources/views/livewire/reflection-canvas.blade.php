{{-- resources/views/livewire/reflection-canvas.blade.php --}}
<div class="min-h-screen bg-[#f3f4f6] relative">
    
    {{-- ヘッダー・サブナビゲーション --}}
    <div class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-md shadow-sm transition">
                カレンダーに戻る
            </a>
        </div>

        <div class="flex-1 max-w-2xl mx-12 p-4 bg-[#f9fafb] rounded-lg border border-gray-200 text-center">
            <p class="text-xs font-bold text-gray-400 mb-1 uppercase tracking-widest">今日の目標</p>
            <p class="text-lg font-bold text-gray-700">{{ $goal->title ?? '目標が設定されていません' }}</p>
        </div>

        <div class="flex items-center space-x-6">
            <div class="text-right">
                <span class="text-[10px] text-gray-400 font-bold block uppercase">Status</span>
                <select class="bg-transparent border-none font-bold text-orange-500 focus:ring-0 p-0 text-sm">
                    <option>進行中</option>
                    <option>完了</option>
                </select>
            </div>
            
            <button wire:click="openModal" class="bg-white border border-gray-200 rounded-lg px-6 py-2 hover:bg-gray-50 transition shadow-sm flex items-center justify-center">
                <span class="text-4xl font-light text-gray-400 leading-none mb-1">+</span>
            </button>
        </div>
    </div>

    {{-- メインキャンバスエリア --}}
    <div class="p-8 h-[calc(100vh-150px)]">
        <div class="grid grid-cols-3 gap-6 h-full">
            
            {{-- 枠1：成功・良かったこと --}}
            <div class="bg-[#e8f5e9] rounded-xl p-6 shadow-sm flex flex-col border border-[#c8e6c9]"
                 x-on:dragover.prevent="" x-on:drop="$wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 1)">
                <h3 class="font-bold text-gray-700 mb-6 flex items-center">成功・良かったこと</h3>
                <div class="flex-grow space-y-4 overflow-y-auto pb-10">
                    @foreach($notes->where('category_id', 1) as $note)
                        <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                             class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 text-sm font-medium cursor-pointer hover:shadow-md transition active:scale-95 relative pr-8">
                            
                            {{-- 👇 変更：ドットをなくし、改行をそのまま反映する --}}
                            <div class="leading-relaxed whitespace-pre-wrap">{{ $note->content }}</div>

                            @if($note->is_starred)
                                <span class="absolute top-2 right-2 text-yellow-400 text-lg">★</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 枠0：未仕分け --}}
            <div class="bg-[#f5f5f5] rounded-xl p-6 shadow-sm flex flex-col border border-[#e0e0e0]"
                 x-on:dragover.prevent="" x-on:drop="$wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 0)">
                <h3 class="font-bold text-gray-700 mb-6">未仕分け</h3>
                <div class="flex-grow space-y-4 overflow-y-auto pb-10">
                    @foreach($notes->where('category_id', 0) as $note)
                        <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                             class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 text-sm font-medium cursor-pointer hover:shadow-md transition active:scale-95 border-l-4 border-l-gray-400 relative pr-8">
                            
                            {{-- 👇 変更：ドットをなくし、改行をそのまま反映する --}}
                            <div class="leading-relaxed whitespace-pre-wrap">{{ $note->content }}</div>

                            @if($note->is_starred)
                                <span class="absolute top-2 right-2 text-yellow-400 text-lg">★</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 右：学び と 明日やる --}}
            <div class="grid grid-rows-2 gap-6 h-full">
                
                {{-- 枠2：学び・改善 --}}
                <div class="bg-[#fce4ec] rounded-xl p-6 shadow-sm flex flex-col border border-[#f8bbd0]"
                     x-on:dragover.prevent="" x-on:drop="$wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 2)">
                    <h3 class="font-bold text-gray-700 mb-4">学び・改善</h3>
                    <div class="flex-grow space-y-3 overflow-y-auto pb-10">
                        @foreach($notes->where('category_id', 2) as $note)
                            <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                                 class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 text-sm font-medium cursor-pointer hover:shadow-md transition active:scale-95 relative pr-8">
                                
                                {{-- 👇 変更：ドットをなくし、改行をそのまま反映する --}}
                                <div class="leading-relaxed whitespace-pre-wrap">{{ $note->content }}</div>

                                @if($note->is_starred)
                                    <span class="absolute top-2 right-2 text-yellow-400 text-lg">★</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 枠3：明日やる --}}
                <div class="bg-[#e3f2fd] rounded-xl p-6 shadow-sm flex flex-col border border-[#bbdefb]"
                     x-on:dragover.prevent="" x-on:drop="$wire.updateNoteCategory(event.dataTransfer.getData('noteId'), 3)">
                    <h3 class="font-bold text-gray-700 mb-4">明日やる</h3>
                    <div class="flex-grow space-y-3 overflow-y-auto pb-10">
                        @foreach($notes->where('category_id', 3) as $note)
                            <div draggable="true" x-on:dragstart="event.dataTransfer.setData('noteId', {{ $note->id }})" wire:click="editNote({{ $note->id }})"
                                 class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 text-sm font-medium cursor-pointer hover:shadow-md transition active:scale-95 relative pr-8">
                                
                                {{-- 👇 変更：ドットをなくし、改行をそのまま反映する --}}
                                <div class="leading-relaxed whitespace-pre-wrap">{{ $note->content }}</div>

                                @if($note->is_starred)
                                    <span class="absolute top-2 right-2 text-yellow-400 text-lg">★</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ゴミ箱ドロップエリア --}}
    <div x-data="{ isHovered: false }"
         x-on:dragover.prevent="isHovered = true"
         x-on:dragleave="isHovered = false"
         x-on:drop="isHovered = false; if(confirm('この付箋を削除してもよろしいですか？')) { $wire.deleteNote(event.dataTransfer.getData('noteId')) }"
         :class="isHovered ? 'bg-red-500 text-white scale-110 shadow-2xl' : 'bg-white text-red-400 border-2 border-red-100 shadow-md hover:bg-red-50'"
         class="fixed bottom-8 left-1/2 transform -translate-x-1/2 w-72 h-14 rounded-full flex items-center justify-center transition-all duration-300 z-40 opacity-90 hover:opacity-100">
        
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        <span class="text-sm font-bold tracking-widest">ここにドロップして削除</span>
    </div>

    {{-- 付箋作成/編集モーダル --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="closeModal" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

            <div class="relative bg-[#d1d5db] w-full max-w-md rounded-3xl shadow-2xl p-8 flex flex-col overflow-hidden">
                <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">
                    {{ $editingNoteId ? '付箋の編集' : '付箋の作成' }}
                </h2>

                <div class="bg-white rounded-xl p-4 shadow-inner mb-6 relative">
                    <textarea wire:model="noteContent" rows="5" placeholder="ここにメモ..." 
                              class="w-full border-none focus:ring-0 text-gray-600 font-medium resize-none"></textarea>
                    
                    <button wire:click="$toggle('isStarred')" class="absolute bottom-4 right-4 transition-transform active:scale-125">
                        <svg class="w-8 h-8 {{ $isStarred ? 'text-yellow-400 fill-current' : 'text-gray-300' }}" 
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </button>
                </div>

                <div class="mb-8">
                    <p class="text-sm font-bold text-gray-500 mb-3 text-center uppercase tracking-widest">頻出項目のタグを表示</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach($frequentTags as $tag)
                            <button wire:click="addTag('{{ $tag }}')" 
                                    class="px-4 py-1.5 bg-gray-100 hover:bg-white text-gray-500 text-xs font-bold rounded-full border border-gray-200 transition">
                                #{{ $tag }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-4">
                    <button wire:click="saveNote" class="flex-1 py-4 bg-[#66bb6a] hover:bg-[#4caf50] text-white font-black rounded-2xl shadow-lg transition transform hover:-translate-y-1">
                        {{ $editingNoteId ? '更新する' : '作成' }}
                    </button>
                    <button wire:click="closeModal" class="flex-1 py-4 bg-[#ef5350] hover:bg-[#f44336] text-white font-black rounded-2xl shadow-lg transition transform hover:-translate-y-1">
                        キャンセル
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>