<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class ProfileCompletion extends Component
{
    public function getCompletionDataProperty()
    {
        $user = auth()->user();
        $profile = $user->profile;

        $completionSteps = [
            [
                'key' => 'basic_info',
                'title' => 'Podstawowe informacje',
                'description' => 'Imię, nazwisko, telefon',
                'completed' => $profile && $profile->first_name && $profile->last_name && $profile->phone,
                'route' => 'profile.edit',
                'icon' => '👤',
                'priority' => 'high'
            ],
            [
                'key' => 'bio',
                'title' => 'Opis profilu',
                'description' => 'Opowiedz o sobie',
                'completed' => $profile && $profile->bio && strlen($profile->bio) >= 50,
                'route' => 'profile.edit',
                'icon' => '📝',
                'priority' => 'medium'
            ],
            [
                'key' => 'avatar',
                'title' => 'Zdjęcie profilowe',
                'description' => 'Dodaj swoje zdjęcie',
                'completed' => $profile && $profile->avatar_url,
                'route' => 'profile.edit',
                'icon' => '📸',
                'priority' => 'medium'
            ]
        ];

        // Additional steps for sitters
        if ($user->isSitter()) {
            $completionSteps = array_merge($completionSteps, [
                [
                    'key' => 'services',
                    'title' => 'Dodaj usługi',
                    'description' => 'Stwórz oferty opieki',
                    'completed' => $user->services()->count() > 0,
                    'route' => 'sitter-services.create',
                    'icon' => '🛠️',
                    'priority' => 'high'
                ],
                [
                    'key' => 'availability',
                    'title' => 'Ustaw dostępność',
                    'description' => 'Skonfiguruj kalendarz',
                    'completed' => false, // TODO: Check if availability is set
                    'route' => 'availability.calendar',
                    'icon' => '📅',
                    'priority' => 'medium'
                ],
                [
                    'key' => 'verification',
                    'title' => 'Weryfikacja konta',
                    'description' => 'Potwierdź swoją tożsamość',
                    'completed' => $profile && $profile->is_verified,
                    'route' => 'verification.request',
                    'icon' => '✅',
                    'priority' => 'high'
                ]
            ]);
        }

        // Additional steps for owners
        if ($user->isOwner()) {
            $completionSteps = array_merge($completionSteps, [
                [
                    'key' => 'pets',
                    'title' => 'Dodaj pupila',
                    'description' => 'Zarejestruj swoje zwierzę',
                    'completed' => $user->pets()->count() > 0,
                    'route' => 'pets.create',
                    'icon' => '🐾',
                    'priority' => 'high'
                ],
                [
                    'key' => 'emergency_contact',
                    'title' => 'Kontakt awaryjny',
                    'description' => 'Dodaj numer alarmowy',
                    'completed' => $profile && $profile->emergency_contact,
                    'route' => 'profile.edit',
                    'icon' => '🚨',
                    'priority' => 'medium'
                ]
            ]);
        }

        $completed = collect($completionSteps)->where('completed', true)->count();
        $total = count($completionSteps);
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'steps' => $completionSteps,
            'completed' => $completed,
            'total' => $total,
            'percentage' => $percentage,
            'remaining' => $total - $completed
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.profile-completion');
    }
}