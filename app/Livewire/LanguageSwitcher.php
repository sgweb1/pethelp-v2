<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $availableLocales = [
        'pl' => [
            'name' => 'Polski',
            'flag' => '🇵🇱'
        ],
        'en' => [
            'name' => 'English',
            'flag' => '🇬🇧'
        ]
    ];

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage($locale)
    {
        if (!array_key_exists($locale, $this->availableLocales)) {
            return;
        }

        App::setLocale($locale);
        Session::put('locale', $locale);
        $this->currentLocale = $locale;

        $this->dispatch('language-changed', $locale);

        // Refresh the page to apply new translations
        return redirect()->to(request()->fullUrl());
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}