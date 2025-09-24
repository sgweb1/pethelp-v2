<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PetType;

class PetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petTypes = [
            [
                'name' => 'Pies',
                'slug' => 'dog',
                'description' => 'Psy wszystkich ras i rozmiarów',
                'icon' => 'dog',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Kot',
                'slug' => 'cat',
                'description' => 'Koty domowe i rasowe',
                'icon' => 'cat',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Ptak',
                'slug' => 'bird',
                'description' => 'Ptaki domowe i egzotyczne',
                'icon' => 'bird',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Królik',
                'slug' => 'rabbit',
                'description' => 'Króliki domowe',
                'icon' => 'rabbit',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Inne',
                'slug' => 'other',
                'description' => 'Inne zwierzęta domowe',
                'icon' => 'other',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($petTypes as $petType) {
            PetType::updateOrCreate(
                ['slug' => $petType['slug']],
                $petType
            );
        }
    }
}
