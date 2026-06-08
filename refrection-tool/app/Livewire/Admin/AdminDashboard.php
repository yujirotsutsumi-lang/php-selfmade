<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination; // ユーザーが増えた時用のページネーション

class AdminDashboard extends Component
{
    use WithPagination;

    public function render()
    {
        $users = User::where('is_admin', false)
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('livewire.admin.admin-dashboard', [
            'users' => $users
        ])->layout('layouts.app');
    }
}