<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $eventTypes = [
            [
                'name' => 'Spacer',
                'slug' => 'spacer',
                'description' => 'Wspólne spacery z zwierzętami w parkach, lesie lub innych ciekawych miejscach.',
                'icon' => 'walking',
                'color' => '#10B981',
                'sort_order' => 1,
            ],
            [
                'name' => 'Sesja treningowa',
                'slug' => 'sesja-treningowa',
                'description' => 'Zajęcia treningowe dla zwierząt - nauka posłuszeństwa, sztuczek i socjalizacji.',
                'icon' => 'academic-cap',
                'color' => '#3B82F6',
                'sort_order' => 2,
            ],
            [
                'name' => 'Socjalizacja',
                'slug' => 'socjalizacja',
                'description' => 'Spotkania mające na celu socjalizację zwierząt - zabawa i interakcje z innymi.',
                'icon' => 'users',
                'color' => '#F59E0B',
                'sort_order' => 3,
            ],
            [
                'name' => 'Wizyta u weterynarza',
                'slug' => 'wizyta-u-weterynarza',
                'description' => 'Wspólne wizyty u weterynarza - wsparcie dla właścicieli i zwierząt.',
                'icon' => 'heart',
                'color' => '#EF4444',
                'sort_order' => 4,
            ],
            [
                'name' => 'Zabawa na wybiegu',
                'slug' => 'zabawa-na-wybiegu',
                'description' => 'Spotkania na psich wybiegach - swobodna zabawa w bezpiecznym środowisku.',
                'icon' => 'play',
                'color' => '#8B5CF6',
                'sort_order' => 5,
            ],
            [
                'name' => 'Pielęgnacja',
                'slug' => 'pielegnacja',
                'description' => 'Sesje pielęgnacyjne - strzyżenie, kąpiele, pedicure dla zwierząt.',
                'icon' => 'sparkles',
                'color' => '#EC4899',
                'sort_order' => 6,
            ],
            [
                'name' => 'Wypoczynek',
                'slug' => 'wypoczynek',
                'description' => 'Spokojne spotkania dla starszych zwierząt lub tych potrzebujących relaksu.',
                'icon' => 'moon',
                'color' => '#6B7280',
                'sort_order' => 7,
            ],
            [
                'name' => 'Transport',
                'slug' => 'transport',
                'description' => 'Wspólny transport zwierząt - podróże, przeprowadzki, wizyty.',
                'icon' => 'truck',
                'color' => '#F97316',
                'sort_order' => 8,
            ],
            [
                'name' => 'Inne',
                'slug' => 'inne',
                'description' => 'Inne typy spotkań nie pasujące do powyższych kategorii.',
                'icon' => 'ellipsis-horizontal',
                'color' => '#64748B',
                'sort_order' => 9,
            ],
        ];

        foreach ($eventTypes as $eventType) {
            EventType::create($eventType);
        }
    }
}
