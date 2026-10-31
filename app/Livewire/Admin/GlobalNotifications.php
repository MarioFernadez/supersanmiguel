<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class GlobalNotifications extends Component
{
    public $unreadCount = 0;
    public $lowStockCount = 0;

    public function mount()
    {
        $this->loadUnreadData();
    }

    public function loadUnreadData()
    {
        $this->lowStockCount = \App\Models\Product::where('is_active', true)
            ->where('manage_stock', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $this->unreadCount = 0;
    }

    public function clearNotifications()
    {
        $this->unreadCount = 0;
    }

    public function render()
    {
        return view('livewire.admin.global-notifications');
    }
}
