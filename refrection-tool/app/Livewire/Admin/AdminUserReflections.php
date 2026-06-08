<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\DailyGoal;
use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminUserReflections extends Component
{
    public User $user;
    public $currentMonth;
    public $selectedDate;

    public $activityCounts = [];
    public $starredDates = [];
    public $achievedDates = [];

    public function mount()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $this->currentMonth = Carbon::today()->startOfMonth()->toDateString();
        $this->selectedDate = Carbon::today()->toDateString();
        
        $this->loadMonthActivities(); // 🚀 1ヶ月分のデータを一括取得！
    }

    // 🚀 一般ユーザー側と完全に同じ、超高速な一括取得ロジック
    public function loadMonthActivities()
    {
        $start = Carbon::parse($this->currentMonth)->startOfMonth();
        $end = Carbon::parse($this->currentMonth)->endOfMonth();

        // 1. 日毎の付箋数をまとめて集計
        $activities = Note::where('user_id', $this->user->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get();
        $this->activityCounts = $activities->pluck('count', 'date')->toArray();

        // 2. 今月の★がついている日付をまとめて取得
        $this->starredDates = Note::where('user_id', $this->user->id)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_starred', true)
            ->selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->pluck('date')
            ->toArray();

        // 3. 今月の「目標達成した日付（✓マーク）」をまとめて取得
        $this->achievedDates = DailyGoal::where('user_id', $this->user->id)
            ->whereBetween('target_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 1) // 1 = 完了
            ->pluck('target_date')
            ->toArray();
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->startOfMonth()->toDateString();
        $this->loadMonthActivities();
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->startOfMonth()->toDateString();
        $this->loadMonthActivities();
    }

    public function goToCurrentMonth()
    {
        $this->currentMonth = Carbon::today()->startOfMonth()->toDateString();
        $this->selectedDate = Carbon::today()->toDateString();
        $this->loadMonthActivities();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    public function getColorLevel($date)
    {
        $count = $this->activityCounts[$date] ?? 0;
        if ($count === 0) return 0;
        
        // ⭕ 一般側と完全に一致！2枚ごとにレベルアップ
        $level = (int) ceil($count / 2);
        return max(1, min(9, $level));
    }

    public function hasStarredNote($date)
    {
        return in_array($date, $this->starredDates);
    }

    public function render()
    {
        $goal = DailyGoal::where('user_id', $this->user->id)
            ->where('target_date', $this->selectedDate)
            ->first();

        // その日の付箋を取得
        $notes = Note::where('user_id', $this->user->id)
            ->whereDate('created_at', $this->selectedDate)
            ->get();

        // ⚡ ポーリング（自動更新）のたびに配列も最新に更新する
        $this->loadMonthActivities();

        return view('livewire.admin.admin-user-reflections', [
            'selectedGoalTitle' => $goal ? $goal->title : '目標の記録なし',
            'notes' => $notes,
            'achievedDates' => $this->achievedDates, // Bladeに✓マーク用の配列を渡す
        ])->layout('layouts.admin');
    }
}