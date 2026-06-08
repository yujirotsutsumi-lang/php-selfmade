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
    
    // 目標ステータス管理 (0:進行中, 1:完了)
    public $goalStatus;

    // モーダル管理用の変数
    public $isModalOpen = false;
    public $noteContent = '';
    public $isStarred = false;
    // 💡 変更：初期値は空っぽにしておきます
    public $frequentTags = []; 
    public $editingNoteId = null; 

    public function mount($date = null)
    {
        $this->date = $date ?? now()->toDateString();
        $this->goal = DailyGoal::where('user_id', Auth::id())
                               ->where('target_date', $this->date)
                               ->first();
        
        $this->goalStatus = $this->goal->status ?? 0;

        // ==========================================
        // 💡 追加：設定した「頻出タグ」をデータベースから読み込む
        // ==========================================
        $userTags = Auth::user()->frequent_tags;
        
        // ユーザーがタグを設定している場合はそれを使用し、無ければデフォルトを使用
        if (!empty($userTags) && is_array($userTags)) {
            $this->frequentTags = $userTags;
        } else {
            $this->frequentTags = ['重要', '継続', '要確認', 'ひらめき'];
        }
    }

    // 目標ステータスが変更されたら保存
    public function updatedGoalStatus($value)
    {
        if ($this->goal) {
            $this->goal->status = (int) $value;
            $this->goal->save();
        }
    }

    // 新規作成モーダルを開く
    public function openModal()
    {
        $this->resetInput();
        $this->isModalOpen = true;
    }

    // 編集モードでモーダルを開く
    public function editNote($noteId)
    {
        $note = Note::where('user_id', Auth::id())->find($noteId);
        if ($note) {
            $this->editingNoteId = $note->id;
            $this->noteContent = $note->content;
            $this->isStarred = $note->is_starred;
            $this->isModalOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->noteContent = '';
        $this->isStarred = false;
        $this->editingNoteId = null;
    }

    public function addTag($tag)
    {
        $this->noteContent .= " #{$tag}";
    }

    // 付箋の保存（新規 or 更新）
    public function saveNote()
    {
        $this->validate(['noteContent' => 'required|max:200']);

        if ($this->editingNoteId) {
            $note = Note::where('user_id', Auth::id())->find($this->editingNoteId);
            if ($note) {
                $note->content = $this->noteContent;
                $note->is_starred = $this->isStarred;
                $note->save();
            }
        } else {
            $note = new Note();
            $note->user_id = Auth::id();
            $note->category_id = 0; // デフォルト：未仕分け
            $note->content = $this->noteContent;
            $note->is_starred = $this->isStarred;
            $note->created_at = $this->date . ' ' . now()->format('H:i:s');
            $note->save();
        }

        $this->closeModal();
    }

    // ドラッグ＆ドロップ移動
    public function updateNoteCategory($noteId, $newCategoryId)
    {
        $note = Note::where('user_id', Auth::id())->find($noteId);
        if ($note) {
            $note->category_id = $newCategoryId;
            $note->save();
        }
    }

    // ゴミ箱削除
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