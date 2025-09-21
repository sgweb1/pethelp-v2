<?php

namespace Database\Seeders;

use App\Models\MapItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetSitterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Tworzenie 1000 pet sitterów...');

        // Ensure we have enough users
        $userCount = User::count();
        if ($userCount < 50) {
            $this->command->warn('Tworzenie dodatkowych użytkowników...');

            // Create users one by one to handle duplicates gracefully
            for ($i = 0; $i < 100; $i++) {
                try {
                    User::factory()->create();
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // Skip duplicate email, continue with next user
                    continue;
                }
            }
        }

        // Create 1000 pet sitters
        MapItem::factory()
            ->count(1000)
            ->petSitter()
            ->create();

        $this->command->info('Utworzono 1000 pet sitterów!');

        // Show current stats
        $totalPetSitters = MapItem::where('content_type', 'pet_sitter')->count();
        $this->command->info("Łączna liczba pet sitterów w bazie: {$totalPetSitters}");
    }
}
