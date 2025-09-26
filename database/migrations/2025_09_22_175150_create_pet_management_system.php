<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sprawdź czy to fresh migration czy update
        $isFreshInstallation = !Schema::hasTable('pet_types') && !Schema::hasColumn('pets', 'pet_type_id');

        if ($isFreshInstallation) {
            // Fresh installation - create everything from scratch
            $this->createPetTypesTable();
            $this->createPetsTableWithRelations();
            $this->seedPetTypes();
        } else {
            // Existing installation - tylko ensure that relations are correct
            $this->ensurePetTypesTable();
            $this->ensurePetsTableRelations();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraint and pet_type_id column from pets
        if (Schema::hasColumn('pets', 'pet_type_id')) {
            Schema::table('pets', function (Blueprint $table) {
                $table->dropForeign(['pet_type_id']);
                $table->dropColumn('pet_type_id');
            });
        }

        // Add back enum column if it doesn't exist
        if (!Schema::hasColumn('pets', 'type')) {
            Schema::table('pets', function (Blueprint $table) {
                $table->enum('type', ['dog', 'cat', 'bird', 'rabbit', 'other'])->after('name');
            });
        }

        // Drop pet_types table
        Schema::dropIfExists('pet_types');
    }

    private function createPetTypesTable(): void
    {
        Schema::create('pet_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    private function createPetsTableWithRelations(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users');
            $table->string('name');
            $table->foreignId('pet_type_id')->constrained('pet_types');
            $table->string('breed')->nullable();
            $table->enum('size', ['small', 'medium', 'large'])->index();
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->text('description')->nullable();
            $table->json('special_needs')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    private function ensurePetTypesTable(): void
    {
        if (!Schema::hasTable('pet_types')) {
            $this->createPetTypesTable();
            $this->seedPetTypes();
        }
    }

    private function ensurePetsTableRelations(): void
    {
        // This method ensures pets table has proper relations
        // Most of the work should already be done by previous migrations

        if (!Schema::hasColumn('pets', 'pet_type_id')) {
            Schema::table('pets', function (Blueprint $table) {
                $table->foreignId('pet_type_id')->nullable()->after('type')->constrained('pet_types');
            });

            // Migrate existing data
            $this->migratePetTypeData();

            Schema::table('pets', function (Blueprint $table) {
                $table->foreignId('pet_type_id')->nullable(false)->change();
                if (Schema::hasColumn('pets', 'type')) {
                    $table->dropColumn('type');
                }
            });
        }
    }

    private function migratePetTypeData(): void
    {
        DB::statement("
            UPDATE pets p
            JOIN pet_types pt ON (
                (p.type = 'dog' AND pt.slug = 'dog') OR
                (p.type = 'cat' AND pt.slug = 'cat') OR
                (p.type = 'bird' AND pt.slug = 'bird') OR
                (p.type = 'rabbit' AND pt.slug = 'rabbit') OR
                (p.type = 'other' AND pt.slug = 'other')
            )
            SET p.pet_type_id = pt.id
            WHERE p.pet_type_id IS NULL
        ");
    }

    private function seedPetTypes(): void
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
            DB::table('pet_types')->updateOrInsert(
                ['slug' => $petType['slug']],
                array_merge($petType, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
};