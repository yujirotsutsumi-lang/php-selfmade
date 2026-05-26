<?php

use App\Livewire\CalendarView;
use App\Livewire\ReflectionCanvas; // 👈 エラーの原因はコレが無いこと！
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// カレンダー（ダッシュボード）画面
Route::get('dashboard', CalendarView::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 振り返りキャンバス画面
Route::get('reflection/{date}', ReflectionCanvas::class)
    ->middleware(['auth', 'verified'])
    ->name('reflection');

// プロフィール画面
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';