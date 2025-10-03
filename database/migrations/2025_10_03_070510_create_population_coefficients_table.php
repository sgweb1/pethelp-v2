<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migracja tabeli współczynników korekcyjnych dla estymacji populacji.
 *
 * Tabela przechowuje konfigurowalne współczynniki używane do skorygowania
 * danych z siatki populacyjnej Eurostat 2021 w celu oszacowania rzeczywistej
 * liczby osób przebywających w obszarze.
 *
 * @see POPULATION_ESTIMATION_COEFFICIENTS.md - pełna dokumentacja
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('population_coefficients', function (Blueprint $table) {
            $table->id();

            // Kategoria miejscowości (X0, X1, X2, X3, X4)
            $table->string('category', 10)->unique();

            // Zakres populacji dla kategorii
            $table->integer('population_min')->default(0);
            $table->integer('population_max')->nullable();

            // Podstawowy współczynnik korekty (k_base)
            $table->decimal('k_base_min', 4, 2)->default(1.00);
            $table->decimal('k_base_max', 4, 2)->default(1.00);

            // Dodatkowe współczynniki korekcyjne
            $table->decimal('k_students', 4, 2)->default(0.00)->comment('Korekta dla miast uniwersyteckich');
            $table->decimal('k_tourism', 4, 2)->default(0.00)->comment('Korekta dla ruchu turystycznego');
            $table->decimal('k_commuters', 4, 2)->default(0.00)->comment('Korekta dla ruchu dojazdowego');
            $table->decimal('k_buildings', 4, 2)->default(0.00)->comment('Korekta dla gęstości zabudowy');

            // Opis i metadane
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();

            // Indeksy
            $table->index('population_min');
            $table->index('active');
        });

        // Wstaw domyślne wartości według dokumentacji
        DB::table('population_coefficients')->insert([
            [
                'category' => 'X0',
                'population_min' => 0,
                'population_max' => 5000,
                'k_base_min' => 1.00,
                'k_base_max' => 1.00,
                'k_students' => 0.00,
                'k_tourism' => 0.00,
                'k_commuters' => 0.00,
                'k_buildings' => 0.00,
                'description' => 'Wieś / bardzo mała miejscowość - praktycznie brak ruchu',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'X1',
                'population_min' => 5000,
                'population_max' => 30000,
                'k_base_min' => 1.02,
                'k_base_max' => 1.02,
                'k_students' => 0.02,
                'k_tourism' => 0.02,
                'k_commuters' => 0.03,
                'k_buildings' => 0.02,
                'description' => 'Małe miasto - lekki ruch dojezdny/zakupy',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'X2',
                'population_min' => 30000,
                'population_max' => 100000,
                'k_base_min' => 1.05,
                'k_base_max' => 1.05,
                'k_students' => 0.04,
                'k_tourism' => 0.03,
                'k_commuters' => 0.05,
                'k_buildings' => 0.03,
                'description' => 'Średnie miasto - większy ruch dzienny, studenci, usługi',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'X3',
                'population_min' => 100000,
                'population_max' => 500000,
                'k_base_min' => 1.10,
                'k_base_max' => 1.25,
                'k_students' => 0.06,
                'k_tourism' => 0.05,
                'k_commuters' => 0.10,
                'k_buildings' => 0.05,
                'description' => 'Duże miasto - znaczny ruch dojazdowy, pracownicy, turyści',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'X4',
                'population_min' => 500000,
                'population_max' => null,
                'k_base_min' => 1.20,
                'k_base_max' => 1.50,
                'k_students' => 0.08,
                'k_tourism' => 0.10,
                'k_commuters' => 0.15,
                'k_buildings' => 0.08,
                'description' => 'Metropolia - aglomeracje, ruch międzygminny, turyści, studenci',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_coefficients');
    }
};
