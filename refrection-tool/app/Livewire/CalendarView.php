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
    public $currentMonth;
    public $selectedDate;
    public $isToday;

    // 目標設定（右パネル）用
    public $isEditingGoal = false;
    public $newGoalTitle = '';
    public $goal;
    public $notes;

    public $goalStatus;

    // ヒートマップ集計用キャッシュ
    public $activityCounts = [];
    public $maxActivity = 0;

    // 今月の★付き日付・目標達成日を入れておく配列
    public $starredDates = [];
    public $achievedDates = [];

    // グラフ表示用のデータ（成功, 学び, 行動 の数）
    public $monthlyChartData = [0, 0, 0];

    // 🚀 【追加】過去の「明日へのアクション」を保持するプロパティ
    public $previousActions = [];
    public $previousActionDate = null;

    public function mount()
    {
        $this->currentMonth = Carbon::today()->startOfMonth()->toDateString();
        $this->selectedDate = Carbon::today()->toDateString();

        $this->loadMonthActivities();
        $this->loadDateData();
    }

    public function loadDateData()
    {
        $this->isToday = ($this->selectedDate === Carbon::today()->toDateString());

        $this->goal = DailyGoal::where('user_id', Auth::id())
            ->where('target_date', $this->selectedDate)
            ->first();

        $this->goalStatus = $this->goal ? $this->goal->status : 0;

        $this->notes = Note::where('user_id', Auth::id())
            ->whereDate('created_at', $this->selectedDate)
            ->get();

        // 🚀 【追加】選択日より前で、最後に「明日へのアクション（カテゴリ3）」を書いた日を探す
        $lastActionNote = Note::where('user_id', Auth::id())
            ->where('category_id', 3)
            ->whereDate('created_at', '<', $this->selectedDate)
            ->latest('created_at') // 一番新しい順
            ->first();

        if ($lastActionNote) {
            // 見つかったら日付をフォーマットして保存（例：6/5）
            $this->previousActionDate = $lastActionNote->created_at->format('n/j');

            // その日の「明日へのアクション」をすべて取得
            $this->previousActions = Note::where('user_id', Auth::id())
                ->where('category_id', 3)
                ->whereDate('created_at', $lastActionNote->created_at->toDateString())
                ->get();
        } else {
            // 過去に一度も書いていない場合は空にする
            $this->previousActions = collect();
            $this->previousActionDate = null;
        }

        $this->isEditingGoal = false;
    }

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

        // 2. 今月の★がついている日付を取得
        $this->starredDates = Note::where('user_id', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            ->where('is_starred', true)
            ->selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->pluck('date')
            ->toArray();

        // 3. 今月の「目標達成した日付」を取得
        $this->achievedDates = DailyGoal::where('user_id', Auth::id())
            ->whereBetween('target_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 1)
            ->pluck('target_date')
            ->toArray();

        // 4. 今月の付箋カテゴリ別の割合を集計（グラフ用）
        $categoryCounts = Note::where('user_id', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->pluck('count', 'category_id')
            ->toArray();

        // グラフ用に [成功の数, 学びの数, 行動の数] の配列を作る
        $this->monthlyChartData = [
            $categoryCounts[1] ?? 0, // 1:成功
            $categoryCounts[2] ?? 0, // 2:学び
            $categoryCounts[3] ?? 0, // 3:行動
        ];
    }

    public function getColorLevel($date)
    {
        $count = $this->activityCounts[$date] ?? 0;
        if ($count === 0) return 0;
        $level = (int) ceil($count / 2);
        return max(1, min(9, $level));
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadDateData();
    }

    public function previousMonth()
    {
        usleep(300000);
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->startOfMonth()->toDateString();
        $this->loadMonthActivities();
    }

    public function nextMonth()
    {
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
        $this->loadMonthActivities();
    }

    public function updatedGoalStatus($value)
    {
        $this->goal = DailyGoal::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'target_date' => $this->selectedDate,
            ],
            [
                'title' => $this->goal ? $this->goal->title : '今日の目標',
                'status' => $value,
            ]
        );

        $this->loadDateData();
        $this->loadMonthActivities();
    }

    public function render()
    {
        return view('livewire.calendar-view')
            ->layout('layouts.app');
    }

    public function hasStarredNote($date)
    {
        return in_array($date, $this->starredDates);
    }
}
