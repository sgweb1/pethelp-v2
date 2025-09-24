<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class NotificationToast extends Component
{
    public $notifications = [];

    #[On('show-toast')]
    public function showToast($type, $message, $duration = 5000)
    {
        $id = uniqid();

        $this->notifications[] = [
            'id' => $id,
            'type' => $type, // success, error, warning, info
            'message' => $message,
            'duration' => $duration,
        ];

        // Auto-hide after duration
        $this->dispatch('hide-toast-after', ['id' => $id, 'duration' => $duration]);
    }

    #[On('show-success-alert')]
    public function showSuccessAlert($message, $duration = 4000)
    {
        $this->showToast('success', $message, $duration);
    }

    #[On('show-error-alert')]
    public function showErrorAlert($message, $duration = 6000)
    {
        $this->showToast('error', $message, $duration);
    }

    #[On('hide-toast')]
    public function hideToast($id)
    {
        $this->notifications = array_filter($this->notifications, function($notification) use ($id) {
            return $notification['id'] !== $id;
        });
    }

    public function dismissToast($id)
    {
        $this->hideToast($id);
    }

    public function render()
    {
        return view('livewire.notification-toast');
    }
}
