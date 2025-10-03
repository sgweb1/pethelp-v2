<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model współczynników korekcyjnych dla estymacji populacji.
 *
 * Przechowuje konfigurowalne współczynniki używane do skorygowania
 * danych z siatki populacyjnej Eurostat 2021 w celu oszacowania
 * rzeczywistej liczby osób przebywających w obszarze.
 *
 * @property string $category Kategoria miejscowości (X0-X4)
 * @property int $population_min Minimalna liczba mieszkańców
 * @property int|null $population_max Maksymalna liczba mieszkańców
 * @property float $k_base_min Minimalny podstawowy współczynnik
 * @property float $k_base_max Maksymalny podstawowy współczynnik
 * @property float $k_students Współczynnik dla studentów
 * @property float $k_tourism Współczynnik dla turystyki
 * @property float $k_commuters Współczynnik dla dojazdów
 * @property float $k_buildings Współczynnik dla zabudowy
 * @property string|null $description Opis kategorii
 * @property bool $active Czy aktywny
 *
 * @see POPULATION_ESTIMATION_COEFFICIENTS.md - pełna dokumentacja
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PopulationCoefficient extends Model
{
    /**
     * Pola które mogą być masowo przypisywane.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category',
        'population_min',
        'population_max',
        'k_base_min',
        'k_base_max',
        'k_students',
        'k_tourism',
        'k_commuters',
        'k_buildings',
        'description',
        'active',
    ];

    /**
     * Rzutowanie atrybutów na odpowiednie typy.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'population_min' => 'integer',
            'population_max' => 'integer',
            'k_base_min' => 'float',
            'k_base_max' => 'float',
            'k_students' => 'float',
            'k_tourism' => 'float',
            'k_commuters' => 'float',
            'k_buildings' => 'float',
            'active' => 'boolean',
        ];
    }

    /**
     * Pobiera współczynniki dla podanej wielkości miasta.
     *
     * @param  int  $population  Liczba mieszkańców miasta
     */
    public static function forPopulation(int $population): ?self
    {
        return self::where('active', true)
            ->where('population_min', '<=', $population)
            ->where(function ($query) use ($population) {
                $query->whereNull('population_max')
                    ->orWhere('population_max', '>=', $population);
            })
            ->first();
    }

    /**
     * Pobiera współczynniki dla kategorii.
     *
     * @param  string  $category  Kategoria (X0-X4)
     */
    public static function forCategory(string $category): ?self
    {
        return self::where('category', $category)
            ->where('active', true)
            ->first();
    }

    /**
     * Oblicza średni k_base dla kategorii.
     */
    public function getAverageKBase(): float
    {
        return ($this->k_base_min + $this->k_base_max) / 2;
    }

    /**
     * Oblicza pełny współczynnik korekcyjny.
     *
     * @param  array  $context  Kontekst (np. czy miasto ma uniwersytet, turystykę itp.)
     */
    public function calculateTotalCorrection(array $context = []): float
    {
        $k_base = $this->getAverageKBase();

        // Jeśli kontekst określa konkretny k_base, użyj go
        if (isset($context['k_base'])) {
            $k_base = max($this->k_base_min, min($this->k_base_max, $context['k_base']));
        }

        // Oblicz dodatkowe korekty
        $additionalCorrections = 0;

        if ($context['has_university'] ?? false) {
            $additionalCorrections += $this->k_students;
        }

        if ($context['is_tourist_destination'] ?? false) {
            $additionalCorrections += $this->k_tourism;
        }

        if ($context['is_commuter_hub'] ?? false) {
            $additionalCorrections += $this->k_commuters;
        }

        if (($context['building_density_ratio'] ?? 1.0) > 1.2) {
            $additionalCorrections += $this->k_buildings;
        }

        // Wzór: k_base × (1 + suma_korekt)
        return $k_base * (1 + $additionalCorrections);
    }
}
