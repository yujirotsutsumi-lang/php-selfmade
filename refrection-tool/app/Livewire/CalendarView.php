<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DailyGoal;
use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarView extends Component
{
    public $selectedDate; // 選択された日付 (YYYY-MM-DD)
    public $currentMonth; // カレンダー表示用の基準月 (YYYY-MM-DD)
    public $newGoalTitle = '';       // 入力された目標のテキスト
    public $isEditingGoal = false;   // 編集モードかどうかの判定フラグ

    public function mount()
    {
        // 初期状態は今日を選択
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        // Livewireでエラーを防ぐため、オブジェクトではなく文字列(Y-m-d)で保持する
        $this->currentMonth = Carbon::today()->startOfMonth()->format('Y-m-d');
    }

    // ==========================================
    // カレンダーの月移動ロジック
    // ==========================================

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->format('Y-m-d');
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->format('Y-m-d');
    }

    public function goToCurrentMonth()
    {
        $this->currentMonth = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->selectedDate = Carbon::today()->format('Y-m-d');
    }

    // ==========================================
    // 日付選択と色の計算ロジック
    // ==========================================

    public function selectDate($date)
    {
        // 未来の日付は選択させない（パターンB）
        if (Carbon::parse($date)->isFuture()) {
            return;
        }
        $this->selectedDate = $date;
    }

    public function getColorLevel($date)
    {
        $goal = DailyGoal::where('user_id', Auth::id())
                        ->where('target_date', $date)
                        ->first();

        if (!$goal || $goal->status == 0) return 0; // グレー（未着手）

        $noteCount = Note::where('user_id', Auth::id())
                        ->whereDate('created_at', $date)
                        ->count();
        
        $hasStar = Note::where('user_id', Auth::id())
                        ->whereDate('created_at', $date)
                        ->where('is_starred', true)
                        ->exists();

        if ($hasStar || $noteCount >= 6) return 3; // 濃いオレンジ
        if ($noteCount >= 3) return 2;             // 普通
        return 1;                                  // 薄い
    }

    // ==========================================
    // 目標の保存・編集ロジック
    // ==========================================

    public function editGoal()
    {
        $this->isEditingGoal = true;
        $goal = DailyGoal::where('user_id', Auth::id())
                    ->where('target_date', $this->selectedDate)
                    ->first();
        // 既に目標があれば入力欄にセット、なければ空っぽ
        $this->newGoalTitle = $goal ? $goal->title : ''; 
    }

    public function saveGoal()
    {
        // 1. バリデーション（空っぽや長すぎる文字を防ぐ）
        $this->validate([
            'newGoalTitle' => 'required|max:100',
        ]);

        // 2. DBに保存（updateOrCreate = あれば更新、なければ新規作成）
        DailyGoal::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'target_date' => $this->selectedDate
            ],
            [
                'title' => $this->newGoalTitle,
                'status' => 0 // 未着手
            ]
        );

        // 3. 編集モードを終了して入力欄をクリア
        $this->isEditingGoal = false;
        $this->newGoalTitle = '';
    }

    // ==========================================
    // 画面の描画（Render）
    // ==========================================

    public function render()
    {
        // 選択された日のデータを取得
        $goal = DailyGoal::where('user_id', Auth::id())
                         ->where('target_date', $this->selectedDate)
                         ->first();

        $notes = Note::where('user_id', Auth::id())
                     ->whereDate('created_at', $this->selectedDate)
                     ->get();

        return view('livewire.calendar-view', [
            'goal' => $goal,
            'notes' => $notes,
            'isToday' => $this->selectedDate === Carbon::today()->format('Y-m-d'),
        ])->layout('layouts.app', ['header' => 'カレンダー振り返り']);
    }
}