<?php

use App\Livewire\CalendarView;
use App\Livewire\ReflectionCanvas;
use App\Livewire\AccountSettings;
use App\Livewire\Admin\AdminLogin; 
use App\Livewire\Admin\AdminDashboard; // 🚀 ここもしっかり追加されています
use Illuminate\Support\Facades\Route;

// ログイン前のトップページ（直接ログイン画面へ転送）
Route::redirect('/', '/login');


// ==========================================
// 👑 1. 管理者：ログイン前のルート（別タブから来る場所）
// ==========================================
Route::get('/admin/login', AdminLogin::class)->name('admin.login');


// ==========================================
// 👤 2. 一般ユーザー：ログイン認証済みのグループ
// ==========================================
Route::middleware([
    'auth',
    'verified',
])->group(function () {
    
    // ログイン直後のデフォルト転送先(/dashboard)を、「日付の選択」へ自動転送
    Route::redirect('/dashboard', '/date-select');

    // ID 3: 日付の選択（カレンダー画面）
    Route::get('/date-select', CalendarView::class)->name('date_select');

    // ID 4: メインボード（キャンバス画面：今日の日付で開く用）
    Route::get('/main-board', ReflectionCanvas::class)->name('main_board');
    
    // カレンダーから過去の特定の日付のキャンバスを閲覧・編集する用のルート
    Route::get('/reflection/{date}', ReflectionCanvas::class)->name('reflection');

    // ID 5: 振り返り画面（過去の振り返りを参照・検索する画面）
    Route::get('/history', CalendarView::class)->name('history');

    // ID 7: アカウント設定
    Route::get('/account-settings', AccountSettings::class)->name('account_settings');
});


// ==========================================
// 👑 3. 管理者：ログイン認証済みのグループ
// ==========================================
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    
    // 🎯 ここを「user.reflections」にするだけで、自動的に「admin.user.reflections」になります！
    Route::get('/users/{user}/reflections', \App\Livewire\Admin\AdminUserReflections::class)->name('user.reflections'); 
});

// 🚨 一般ユーザーの認証ルート
require __DIR__.'/auth.php';