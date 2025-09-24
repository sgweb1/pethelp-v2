<?php

namespace App\Livewire\Dashboard;

use App\Models\Event;
use Livewire\Component;

class MyEvents extends Component
{
    public $listeners = ['refreshDashboard' => '$refresh'];

    public function getEventsProperty()
    {
        return auth()->user()->events()
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.my-events');
    }
}
