<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model dla gridów populacyjnych (siatka 1km²).
 *
 * Przechowuje dane o rozmieszczeniu ludności w kratkach 1×1 km
 * pochodzące z Eurostat GEOSTAT. Umożliwia dokładne oszacowanie
 * populacji w dowolnym promieniu.
 *
 * @property int $id
 * @property string $grid_id Identyfikator kratki (np. 1kmN5400E2100)
 * @property float $latitude Szerokość geograficzna środka kratki
 * @property float $longitude Długość geograficzna środka kratki
 * @property int $population Liczba ludności w kratce
 * @property int $year Rok danych
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @author Claude AI Assistant
 *
 * @version 1.0.0
 */
class PopulationGrid extends Model
{
    /**
     * Nazwa tabeli w bazie danych.
     *
     * @var string
     */
    protected $table = 'population_grid';

    /**
     * Atrybuty które mogą być mass-assigned.
     *
     * @var array<string>
     */
    protected $fillable = [
        'grid_id',
        'latitude',
        'longitude',
        'population',
        'year',
    ];

    /**
     * Atrybuty które powinny być castowane do typów natywnych.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'population' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Znajduje wszystkie kratki w danym promieniu od punktu.
     *
     * Używa formuły Haversine do obliczenia dystansu na kuli ziemskiej.
     * Jest to szybsze niż pełne obliczenia spatial MySQL.
     *
     * @param  float  $latitude  Szerokość geograficzna punktu centralnego
     * @param  float  $longitude  Długość geograficzna punktu centralnego
     * @param  int  $radiusKm  Promień w kilometrach
     * @return \Illuminate\Database\Eloquent\Collection Kratki w promieniu
     */
    public static function findInRadius(float $latitude, float $longitude, int $radiusKm): \Illuminate\Database\Eloquent\Collection
    {
        // Optymalizacja: najpierw zawęź zestaw danych przez bounding box
        // 1 stopień = ~111 km, więc obliczamy margines w stopniach
        $latMargin = $radiusKm / 111.0;
        $lngMargin = $radiusKm / (111.0 * cos(deg2rad($latitude)));

        // Formuła Haversine: oblicza dystans na sferze
        // 6371 = promień Ziemi w km
        $query = self::select('*')
            ->where('latitude', '>=', $latitude - $latMargin)
            ->where('latitude', '<=', $latitude + $latMargin)
            ->where('longitude', '>=', $longitude - $lngMargin)
            ->where('longitude', '<=', $longitude + $lngMargin)
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');

        return $query->get();
    }

    /**
     * Oblicza całkowitą populację w kratkach.
     *
     * @param  \Illuminate\Support\Collection  $grids  Kolekcja kratek
     * @return int Suma populacji
     */
    public static function totalPopulation(\Illuminate\Support\Collection $grids): int
    {
        return $grids->sum('population');
    }

    /**
     * Znajduje kratkę po identyfikatorze.
     *
     * @param  string  $gridId  Identyfikator kratki
     */
    public static function findByGridId(string $gridId): ?self
    {
        return self::where('grid_id', $gridId)->first();
    }
}
