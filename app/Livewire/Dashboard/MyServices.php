<?php

namespace App\Livewire\Dashboard;

use App\Models\Service;
use Livewire\Component;

class MyServices extends Component
{
    public $listeners = ['refreshDashboard' => '$refresh', 'service-created' => '$refresh'];

    public function getServicesProperty()
    {
        return auth()->user()->services()
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function toggleServiceStatus($serviceId)
    {
        $service = Service::where('id', $serviceId)
            ->where('sitter_id', auth()->id())
            ->first();

        if ($service) {
            $service->update(['is_active' => !$service->is_active]);
            $this->dispatch('service-updated');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.my-services');
    }
}
