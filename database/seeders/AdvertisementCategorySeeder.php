<?php

namespace Database\Seeders;

use App\Models\AdvertisementCategory;
use Illuminate\Database\Seeder;

class AdvertisementCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ADOPTION CATEGORIES
            [
                'name' => 'Adopcja',
                'slug' => 'adopcja',
                'description' => 'Kategoria główna dla adopcji zwierząt',
                'type' => 'adoption',
                'icon' => 'heart',
                'color' => '#EF4444',
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Adopcja Psa',
                        'slug' => 'adopcja-psa',
                        'description' => 'Psy szukające nowego domu',
                        'type' => 'adoption',
                        'icon' => 'heart',
                        'color' => '#EF4444',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Adopcja Kota',
                        'slug' => 'adopcja-kota',
                        'description' => 'Koty szukające nowego domu',
                        'type' => 'adoption',
                        'icon' => 'heart',
                        'color' => '#F97316',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Adopcja Królika',
                        'slug' => 'adopcja-krolika',
                        'description' => 'Króliki szukające nowego domu',
                        'type' => 'adoption',
                        'icon' => 'heart',
                        'color' => '#F59E0B',
                        'sort_order' => 3,
                    ],
                ],
            ],

            // SALES CATEGORIES
            [
                'name' => 'Sprzedaż',
                'slug' => 'sprzedaz',
                'description' => 'Kategoria główna dla sprzedaży zwierząt',
                'type' => 'sales',
                'icon' => 'currency-dollar',
                'color' => '#059669',
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Sprzedaż Psa',
                        'slug' => 'sprzedaz-psa',
                        'description' => 'Psy na sprzedaż z hodowli',
                        'type' => 'sales',
                        'icon' => 'currency-dollar',
                        'color' => '#059669',
                        'sort_order' => 1,
                        'requires_approval' => true,
                    ],
                    [
                        'name' => 'Sprzedaż Kota',
                        'slug' => 'sprzedaz-kota',
                        'description' => 'Koty na sprzedaż z hodowli',
                        'type' => 'sales',
                        'icon' => 'currency-dollar',
                        'color' => '#DC2626',
                        'sort_order' => 2,
                        'requires_approval' => true,
                    ],
                ],
            ],

            // LOST & FOUND CATEGORIES
            [
                'name' => 'Zaginione i znalezione',
                'slug' => 'zaginione-znalezione',
                'description' => 'Kategoria główna dla zaginionych i znalezionych zwierząt',
                'type' => 'lost_found',
                'icon' => 'exclamation-triangle',
                'color' => '#F59E0B',
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Zaginiony Pies',
                        'slug' => 'zaginiony-pies',
                        'description' => 'Zaginione psy - pomóż je odnaleźć',
                        'type' => 'lost_found',
                        'icon' => 'exclamation-triangle',
                        'color' => '#DC2626',
                        'sort_order' => 1,
                        'max_images' => 5,
                    ],
                    [
                        'name' => 'Znaleziony Pies',
                        'slug' => 'znaleziony-pies',
                        'description' => 'Znalezione psy szukające właściciela',
                        'type' => 'lost_found',
                        'icon' => 'check-circle',
                        'color' => '#10B981',
                        'sort_order' => 3,
                        'max_images' => 5,
                    ],
                ],
            ],

            // SUPPLIES CATEGORIES
            [
                'name' => 'Akcesoria',
                'slug' => 'akcesoria',
                'description' => 'Kategoria główna dla akcesoriów i artykułów dla zwierząt',
                'type' => 'supplies',
                'icon' => 'shopping-bag',
                'color' => '#7C3AED',
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Karma i przysmaki',
                        'slug' => 'karma-przysmaki',
                        'description' => 'Karma sucha, mokra, przysmaki dla zwierząt',
                        'type' => 'supplies',
                        'icon' => 'gift',
                        'color' => '#F59E0B',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Zabawki',
                        'slug' => 'zabawki',
                        'description' => 'Zabawki dla psów, kotów i innych zwierząt',
                        'type' => 'supplies',
                        'icon' => 'puzzle-piece',
                        'color' => '#EC4899',
                        'sort_order' => 2,
                    ],
                ],
            ],

            // SERVICES CATEGORIES
            [
                'name' => 'Usługi',
                'slug' => 'uslugi',
                'description' => 'Kategoria główna dla usług profesjonalnych',
                'type' => 'services',
                'icon' => 'briefcase',
                'color' => '#1F2937',
                'sort_order' => 5,
                'children' => [
                    [
                        'name' => 'Trener Psów',
                        'slug' => 'trener-psow',
                        'description' => 'Profesjonalni trenerzy i behawioraliści',
                        'type' => 'services',
                        'icon' => 'academic-cap',
                        'color' => '#3B82F6',
                        'sort_order' => 1,
                        'requires_approval' => true,
                    ],
                    [
                        'name' => 'Weterynarz',
                        'slug' => 'weterynarz',
                        'description' => 'Usługi weterynaryjne i konsultacje',
                        'type' => 'services',
                        'icon' => 'shield-check',
                        'color' => '#DC2626',
                        'sort_order' => 2,
                        'requires_approval' => true,
                    ],
                    [
                        'name' => 'Grooming',
                        'slug' => 'grooming',
                        'description' => 'Strzyżenie i pielęgnacja zwierząt',
                        'type' => 'services',
                        'icon' => 'scissors',
                        'color' => '#EC4899',
                        'sort_order' => 3,
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = AdvertisementCategory::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                AdvertisementCategory::create($childData);
            }
        }
    }
}
