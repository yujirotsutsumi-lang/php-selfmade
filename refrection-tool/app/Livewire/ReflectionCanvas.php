<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Note;
use App\Models\DailyGoal;
use Illuminate\Support\Facades\Auth;

class ReflectionCanvas extends Component
{
    public $date;
    public $goal;
    
    // モーダル管理用の変数
    public $isModalOpen = false;
    public $noteContent = '';
    public $isStarred = false;
    public $frequentTags = ['重要', '継続', '要確認', 'ひらめき'];
    
    // 👇 追加：現在編集中の付箋IDを記憶する変数
    public $editingNoteId = null; 

    public function mount($date)
    {
        $this->date = $date;
        $this->goal = DailyGoal::where('user_id', Auth::id())
                               ->where('target_date', $this->date)
                               ->first();
    }

    // 「＋」ボタンで新規作成モーダルを開く
    public function openModal()
    {
        $this->resetInput();
        $this->isModalOpen = true;
    }

    // 👇 追加：付箋をクリックして「編集モード」でモーダルを開く
    public function editNote($noteId)
    {
        $note = Note::where('user_id', Auth::id())->find($noteId);
        if ($note) {
            $this->editingNoteId = $note->id;     // 編集中のIDをセット
            $this->noteContent = $note->content;  // 元の文字をセット
            $this->isStarred = $note->is_starred; // 元のスター状態をセット
            $this->isModalOpen = true;            // モーダルを開く
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInput(); // 閉じる時に中身を綺麗にする
    }

    private function resetInput()
    {
        $this->noteContent = '';
        $this->isStarred = false;
        $this->editingNoteId = null; // 新規作成モードに戻す
    }

    public function addTag($tag)
    {
        $this->noteContent .= " #{$tag}";
    }

    // 👇 変更：保存時に「新規作成」か「更新」かを自動で振り分ける
    public function saveNote()
    {
        $this->validate(['noteContent' => 'required|max:200']);

        if ($this->editingNoteId) {
            // 【編集（更新）の場合】
            $note = Note::where('user_id', Auth::id())->find($this->editingNoteId);
            if ($note) {
                $note->content = $this->noteContent;
                $note->is_starred = $this->isStarred;
                $note->save();
            }
        } else {
            // 【新規作成の場合】
            $note = new Note();
            $note->user_id = Auth::id();
            $note->category_id = 0; // デフォルトは未仕分け
            $note->content = $this->noteContent;
            $note->is_starred = $this->isStarred;
            $note->created_at = $this->date . ' ' . now()->format('H:i:s');
            $note->save();
        }

        $this->closeModal();
    }

    // カテゴリーの移動（ドラッグ＆ドロップ）
    public function updateNoteCategory($noteId, $newCategoryId)
    {
        $note = Note::where('user_id', Auth::id())->find($noteId);
        if ($note) {
            $note->category_id = $newCategoryId;
            $note->save();
        }
    }

    // ゴミ箱で削除
    public function deleteNote($noteId)
    {
        $note = Note::where('user_id', Auth::id())->find($noteId);
        if ($note) {
            $note->delete();
        }
    }

    public function render()
    {
        $notes = Note::where('user_id', Auth::id())
                     ->whereDate('created_at', $this->date)
                     ->get();

        return view('livewire.reflection-canvas', [
            'notes' => $notes
        ])->layout('layouts.app', ['header' => '振り返りキャンバス']);
    }
}