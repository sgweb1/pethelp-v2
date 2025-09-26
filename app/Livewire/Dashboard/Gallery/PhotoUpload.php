<?php

namespace App\Livewire\Dashboard\Gallery;

use Livewire\Component;

class PhotoUpload extends Component
{
    public function render()
    {
        return view('livewire.dashboard.gallery.photo-upload')
            ->layout('components.dashboard-layout', [
                'title' => 'Dodaj zdjęcie',
                'activeSection' => 'gallery'
            ]);
    }
}
