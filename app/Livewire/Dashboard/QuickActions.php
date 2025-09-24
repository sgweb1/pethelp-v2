<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class QuickActions extends Component
{
    public function getActionsProperty()
    {
        $user = auth()->user();
        $actions = [];

        if ($user->isOwner()) {
            $actions = [
                [
                    'title' => 'Znajdź opiekuna',
                    'description' => 'Wyszukaj usługi opieki',
                    'icon' => '🔍',
                    'route' => 'search',
                    'color' => 'primary',
                    'featured' => true
                ],
                [
                    'title' => 'Dodaj pupila',
                    'description' => 'Zarejestruj nowe zwierzę',
                    'icon' => '🐾',
                    'route' => 'pets.create',
                    'color' => 'green'
                ],
                [
                    'title' => 'Moje rezerwacje',
                    'description' => 'Zarządzaj zleceniami',
                    'icon' => '📋',
                    'route' => 'bookings',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Profil',
                    'description' => 'Edytuj swoje dane',
                    'icon' => '👤',
                    'route' => 'profile.edit',
                    'color' => 'gray'
                ]
            ];
        }

        if ($user->isSitter()) {
            $actions = [
                [
                    'title' => 'Dodaj usługę',
                    'description' => 'Nowa oferta opieki',
                    'icon' => '➕',
                    'route' => 'sitter-services.create',
                    'color' => 'primary',
                    'featured' => true
                ],
                [
                    'title' => 'Moje usługi',
                    'description' => 'Zarządzaj ofertami',
                    'icon' => '🛠️',
                    'route' => 'services.index',
                    'color' => 'purple'
                ],
                [
                    'title' => 'Kalendarz',
                    'description' => 'Zarządzaj dostępnością',
                    'icon' => '📅',
                    'route' => 'availability.calendar',
                    'color' => 'green'
                ],
                [
                    'title' => 'Moje rezerwacje',
                    'description' => 'Przegląd zleceń',
                    'icon' => '📋',
                    'route' => 'bookings',
                    'color' => 'blue'
                ]
            ];
        }

        // Common actions for all users
        $commonActions = [
            [
                'title' => 'Wiadomości',
                'description' => 'Komunikacja z użytkownikami',
                'icon' => '💬',
                'route' => 'chat',
                'color' => 'indigo',
                'badge' => $user->getUnreadMessagesCount()
            ],
            [
                'title' => 'Powiadomienia',
                'description' => 'Sprawdź aktualności',
                'icon' => '🔔',
                'route' => 'notifications',
                'color' => 'yellow',
                'badge' => $user->notifications()->unread()->count()
            ]
        ];

        return array_merge($actions, $commonActions);
    }

    public function render()
    {
        return view('livewire.dashboard.quick-actions');
    }
}