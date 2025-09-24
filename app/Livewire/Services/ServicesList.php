<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\MapItem;
use Livewire\Component;
use Livewire\Attributes\On;

class ServicesList extends Component
{
    public $confirmingServiceDeletion = false;
    public $serviceToDelete = null;

    public function getServicesProperty()
    {
        return Service::with('category')
            ->where('sitter_id', auth()->id())
            ->latest()
            ->get();
    }

    #[On('confirmServiceDeletion')]
    public function confirmServiceDeletion($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        if ($service->sitter_id !== auth()->id()) {
            abort(403, 'Nie masz uprawnień do usunięcia tej usługi.');
        }

        $this->serviceToDelete = $service;
        $this->confirmingServiceDeletion = true;
    }

    public function deleteService()
    {
        if (!$this->serviceToDelete) {
            return;
        }

        try {
            \DB::transaction(function () {
                MapItem::where('mappable_type', Service::class)
                    ->where('mappable_id', $this->serviceToDelete->id)
                    ->delete();

                $this->serviceToDelete->delete();
            });

            session()->flash('success', 'Usługa została pomyślnie usunięta.');

            $this->confirmingServiceDeletion = false;
            $this->serviceToDelete = null;

        } catch (\Exception $e) {
            \Log::error('Error deleting service: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas usuwania usługi. Spróbuj ponownie.');
        }
    }

    public function cancelDeletion()
    {
        $this->confirmingServiceDeletion = false;
        $this->serviceToDelete = null;
    }

    public function render()
    {
        return view('livewire.services.services-list')->layout('components.dashboard-layout');
    }
}