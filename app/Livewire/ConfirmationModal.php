<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ConfirmationModal extends Component
{
    public $show = false;
    public $title = '';
    public $message = '';
    public $confirmText = 'Potwierdź';
    public $cancelText = 'Anuluj';
    public $confirmClass = 'bg-red-600 hover:bg-red-700';
    public $action = '';
    public $actionParams = [];

    #[On('show-confirmation')]
    public function showConfirmation($title, $message, $action, $params = [], $confirmText = 'Potwierdź', $cancelText = 'Anuluj', $confirmClass = 'bg-red-600 hover:bg-red-700')
    {
        $this->title = $title;
        $this->message = $message;
        $this->action = $action;
        $this->actionParams = $params;
        $this->confirmText = $confirmText;
        $this->cancelText = $cancelText;
        $this->confirmClass = $confirmClass;
        $this->show = true;
    }

    public function confirm()
    {
        if ($this->action) {
            // Dispatch the action event with parameters
            $this->dispatch($this->action, ...$this->actionParams);
        }

        $this->close();
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['title', 'message', 'action', 'actionParams', 'confirmText', 'cancelText', 'confirmClass']);
    }

    public function render()
    {
        return view('livewire.confirmation-modal');
    }
}
