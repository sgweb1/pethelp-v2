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
                'name' => 'Opieka w domu',
                'slug' => 'opieka-w-domu',
                'description' => 'Opieka nad zwierzętami w domu właściciela',
                'icon' => '🏠',
                'sort_order' => 1,
            ],
            [
                'name' => 'Spacery',
                'slug' => 'spacery',
                'description' => 'Regularne spacery i aktywność fizyczna',
                'icon' => '🚶',
                'sort_order' => 2,
            ],
            [
                'name' => 'Opieka w domu opiekuna',
                'slug' => 'opieka-u-opiekuna',
                'description' => 'Zwierzę mieszka u opiekuna przez określony czas',
                'icon' => '🏡',
                'sort_order' => 3,
            ],
            [
                'name' => 'Wizyta kontrolna',
                'slug' => 'wizyta-kontrolna',
                'description' => 'Krótkie wizyty sprawdzające stan zwierzęcia',
                'icon' => '👀',
                'sort_order' => 4,
            ],
            [
                'name' => 'Karmienie',
                'slug' => 'karmienie',
                'description' => 'Regularne karmienie podczas nieobecności właściciela',
                'icon' => '🍽️',
                'sort_order' => 5,
            ],
            [
                'name' => 'Transport weterynaryjny',
                'slug' => 'transport-weterynaryjny',
                'description' => 'Przewóz do kliniki weterynaryjnej',
                'icon' => '🏥',
                'sort_order' => 6,
            ],
            [
                'name' => 'Pielęgnacja',
                'slug' => 'pielegnacja',
                'description' => 'Czesanie, kąpiel i podstawowa pielęgnacja',
                'icon' => '✨',
                'sort_order' => 7,
            ],
            [
                'name' => 'Opieka nocna',
                'slug' => 'opieka-nocna',
                'description' => 'Opieka przez całą noc',
                'icon' => '🌙',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }
    }
}
