<?php

namespace App\Livewire\Dashboard;

use App\Models\Advertisement;
use Livewire\Component;

class MyAdvertisements extends Component
{
    public $listeners = ['refreshDashboard' => '$refresh'];

    public function getAdvertisementsProperty()
    {
        return auth()->user()->advertisements()
            ->with(['advertisementCategory', 'primaryImage'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function toggleAdvertisementStatus($advertisementId)
    {
        $advertisement = Advertisement::where('id', $advertisementId)
            ->where('user_id', auth()->id())
            ->first();

        if ($advertisement) {
            $newStatus = $advertisement->status === 'published' ? 'draft' : 'published';
            $advertisement->update(['status' => $newStatus]);
            $this->dispatch('advertisement-updated');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.my-advertisements');
    }
}
