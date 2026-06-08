<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Note;
use App\Models\DailyGoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarView extends Component
{
    // カレンダー状態管理
    public $currentMonth;      // 表示中の年月（例: '2026-05-01'）
    public $selectedDate;      // 選択中の日付（例: '2026-05-29'）
    public $isToday;           // 選択日が今日かどうか

    // 目標設定（右パネル）用
    public $isEditingGoal = false;
    public $newGoalTitle = '';
    public $goal;
    public $notes;
    
    // 🚀 【追加】セレクトボックスの選択状態を管理するプロパティ
    public $goalStatus;

    // ヒートマップ集計用キャッシュ
    public $activityCounts = [];
    public $maxActivity = 0;

    // 今月の★付き日付・目標達成日を入れておく配列（保管庫）
    public $starredDates = [];
    public $achievedDates = [];

    public function mount()
    {
        // 初期状態は「今月」と「今日」を選択
        $this->currentMonth = Carbon::today()->startOfMonth()->toDateString();
        $this->selectedDate = Carbon::today()->toDateString();
        
        $this->loadMonthActivities();
        $this->loadDateData();
    }

    // 選択された日付の目標と付箋データを読み込む
    public function loadDateData()
    {
        $this->isToday = ($this->selectedDate === Carbon::today()->toDateString());

        // 指定日の目標を取得
        $this->goal = DailyGoal::where('user_id', Auth::id())
            ->where('target_date', $this->selectedDate)
            ->first();

        // 🚀 【追加】右側パネルのセレクトボックスに初期状態（0:進行中 または 1:完了）をセット
        $this->goalStatus = $this->goal ? $this->goal->status : 0;

        // 指定日の付箋一覧を取得
        $this->notes = Note::where('user_id', Auth::id())
            ->whereDate('created_at', $this->selectedDate)
            ->get();
            
        $this->isEditingGoal = false;
    }

    // 表示中の月全体の活動（付箋数 ＆ ★付き日付 ＆ 達成日）をまとめて集計
    public function loadMonthActivities()
    {
        $start = Carbon::parse($this->currentMonth)->startOfMonth();
        $end = Carbon::parse($this->currentMonth)->endOfMonth();

        // 1. 日毎の付箋数をまとめて集計
        $activities = Note::where('user_id', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        $this->activityCounts = $activities->pluck('count', 'date')->toArray();
        $this->maxActivity = $activities->max('count') ?? 0;

        // 2. 【N+1解消】今月の★がついている日付だけを、1回のクエリでまとめて取得！
        $this->starredDates = Note::where('user_id', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            ->where('is_starred', true)
            ->selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->pluck('date')
            ->toArray();

        // 3. 【N+1解消】今月の「目標達成した日付」だけを、1回のクエリでまとめて取得！
        $this->achievedDates = DailyGoal::where('user_id', Auth::id())
            ->whereBetween('target_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 1) // 1 = 完了
            ->pluck('target_date')
            ->toArray();
    }

    public function getColorLevel($date)
    {
        $count = $this->activityCounts[$date] ?? 0;

        if ($count === 0) return 0; // 0枚の場合は色なし（グレー）

        // 付箋3枚ごとにレベルを1上げる（1〜3枚=Lv1, 4〜6枚=Lv2...）
        $level = (int) ceil($count / 2);

        // どれだけたくさん書いても、最大レベルは「9」でストップさせる
        return max(1, min(9, $level));
    }

    // 日付がクリックされたとき
    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadDateData();
    }

    // 月移動ロジック
    public function previousMonth()
    {
        //300,000マイクロ秒（＝0.3秒）処理をストップさせる
        usleep(300000); 
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->startOfMonth()->toDateString();
        $this->loadMonthActivities();
    }

    public function nextMonth()
    {
        //300,000マイクロ秒（＝0.3秒）処理をストップさせる
        usleep(300000); 
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->startOfMonth()->toDateString();
        $this->loadMonthActivities();
    }

    public function goToCurrentMonth()
    {
        $this->currentMonth = Carbon::today()->startOfMonth()->toDateString();
        $this->selectedDate = Carbon::today()->toDateString();
        $this->loadMonthActivities();
        $this->loadDateData();
    }

    // 目標の編集・保存
    public function editGoal()
    {
        $this->newGoalTitle = $this->goal ? $this->goal->title : '';
        $this->isEditingGoal = true;
    }

    public function saveGoal()
    {
        $this->validate(['newGoalTitle' => 'required|max:50']);

        $this->goal = DailyGoal::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'target_date' => $this->selectedDate,
            ],
            [
                'title' => $this->newGoalTitle,
                'status' => $this->goal->status ?? 0,
            ]
        );

        $this->isEditingGoal = false;
        $this->loadDateData();
        $this->loadMonthActivities(); // 🚀 【追加】保存した瞬間にカレンダーを更新！
    }

    // 🚀 【追加】画面のセレクトボックスで「進行中/完了」が切り替わった瞬間に自動で走る処理
    public function updatedGoalStatus($value)
    {
        $this->goal = DailyGoal::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'target_date' => $this->selectedDate,
            ],
            [
                'title' => $this->goal ? $this->goal->title : '今日の目標', // まだ目標がない場合はデフォルト値
                'status' => $value, // 新しいステータス（0:進行中 または 1:完了）
            ]
        );

        $this->loadDateData();
        $this->loadMonthActivities(); // 🚀 ステータス変更時にカレンダーの✓マークを再集計！
    }

    public function render()
    {
        return view('livewire.calendar-view')
            ->layout('layouts.app');
    }

    // 【超高速化】データベースには一切触れず、メモリ上の配列に日付があるか調べるだけに！
    public function hasStarredNote($date)
    {
        return in_array($date, $this->starredDates);
    }
}