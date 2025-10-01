<?php

namespace Database\Seeders; 

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Spacery z psem',
                'slug' => 'spacery',
                'description' => 'Regularne spacery i aktywność fizyczna dla psów',
                'icon' => '🐕',
                'sort_order' => 1,
            ],
            [
                'name' => 'Opieka w domu właściciela',
                'slug' => 'opieka-w-domu',
                'description' => 'Opieka nad zwierzętami w domu właściciela podczas jego nieobecności',
                'icon' => '🏠',
                'sort_order' => 2,
            ],
            [
                'name' => 'Opieka u opiekuna',
                'slug' => 'opieka-u-opiekuna',
                'description' => 'Zwierzę mieszka w domu opiekuna przez określony czas',
                'icon' => '🏡',
                'sort_order' => 3,
            ],
            [
                'name' => 'Opieka nocna',
                'slug' => 'opieka-nocna',
                'description' => 'Nocowanie z zwierzęciem u klienta lub opieka przez całą noc',
                'icon' => '🌙',
                'sort_order' => 4,
            ],
            [
                'name' => 'Transport zwierząt',
                'slug' => 'transport-weterynaryjny',
                'description' => 'Bezpieczny przewóz zwierząt do weterynarza lub innych miejsc',
                'icon' => '🚗',
                'sort_order' => 5,
            ],
            [
                'name' => 'Wizyty u weterynarza',
                'slug' => 'wizyta-kontrolna',
                'description' => 'Wizyty kontrolne i pomoc w opiece weterynaryjnej',
                'icon' => '⚕️',
                'sort_order' => 6,
            ],
            [
                'name' => 'Pielęgnacja',
                'slug' => 'pielegnacja',
                'description' => 'Czesanie, kąpiel, strzyżenie i podstawowa pielęgnacja',
                'icon' => '✂️',
                'sort_order' => 7,
            ],
            [
                'name' => 'Karmienie',
                'slug' => 'karmienie',
                'description' => 'Regularne karmienie i dbanie o dietę zwierzęcia',
                'icon' => '🍽️',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::firstOrCreate(
                ['slug' => $category['slug']], // Znajdź po slug
                $category // Utwórz z tymi danymi
            );
        }
    }
}
