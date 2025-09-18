<?php

namespace Database\Seeders; 

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pet;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first 3 users (not sitters) to create pets for
        $users = User::take(3)->get();

        $pets = [
            [
                'name' => 'Burek',
                'type' => 'dog',
                'breed' => 'Labrador',
                'size' => 'large',
                'age' => 3,
                'gender' => 'male',
                'description' => 'Przyjazny i energiczny pies, uwielbia spacery i zabawę.',
                'special_needs' => ['exercise']
            ],
            [
                'name' => 'Mila',
                'type' => 'cat',
                'breed' => 'Perski',
                'size' => 'medium',
                'age' => 2,
                'gender' => 'female',
                'description' => 'Spokojna kotka, lubi spokój i drapanie.',
                'special_needs' => ['diet']
            ],
            [
                'name' => 'Rex',
                'type' => 'dog',
                'breed' => 'Owczarek niemiecki',
                'size' => 'large',
                'age' => 5,
                'gender' => 'male',
                'description' => 'Bardzo inteligentny pies, dobrze wyszkolony.',
                'special_needs' => []
            ],
            [
                'name' => 'Kicia',
                'type' => 'cat',
                'breed' => 'Dachowiec',
                'size' => 'small',
                'age' => 1,
                'gender' => 'female',
                'description' => 'Młoda kotka, bardzo figlarny i ciekawa świata.',
                'special_needs' => ['training']
            ],
            [
                'name' => 'Puszek',
                'type' => 'rabbit',
                'breed' => 'Królик miniaturowy',
                'size' => 'small',
                'age' => 2,
                'gender' => 'male',
                'description' => 'Spokojny królik, lubi świeżą marchewkę.',
                'special_needs' => ['diet']
            ],
            [
                'name' => 'Charlie',
                'type' => 'dog',
                'breed' => 'Golden Retriever',
                'size' => 'large',
                'age' => 7,
                'gender' => 'male',
                'description' => 'Senior pies, potrzebuje spokojnej opieki.',
                'special_needs' => ['elderly', 'medication']
            ]
        ];

        foreach ($users as $index => $user) {
            // Each user gets 2 pets
            for ($i = 0; $i < 2; $i++) {
                $petIndex = ($index * 2) + $i;
                if (isset($pets[$petIndex])) {
                    Pet::create(array_merge($pets[$petIndex], [
                        'owner_id' => $user->id,
                        'is_active' => true
                    ]));
                }
            }
        }

        echo "Stworzono " . Pet::count() . " zwierząt dla użytkowników testowych.\n";
    }
}
