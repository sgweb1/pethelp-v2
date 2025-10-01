<?php

namespace App\Livewire\Dashboard\Core;

use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Bazowa klasa dla wszystkich komponentów dashboard PetHelp.
 *
 * Zapewnia wspólną funkcjonalność dla komponentów dashboard takich jak
 * cache'owanie danych, obsługę stanów ładowania, formatowanie wartości
 * oraz standardowe listenery eventów.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
abstract class BaseDashboard extends Component
{
    /**
     * Identyfikator cache dla komponentu.
     */
    protected string $cacheKey;

    /**
     * Czas życia cache w sekundach.
     */
    protected int $cacheTtl = 300; // 5 minut

    /**
     * Czy komponent jest w stanie ładowania.
     */
    public bool $loading = false;

    /**
     * Czy automatycznie odświeżać dane.
     */
    public bool $autoRefresh = true;

    /**
     * Interwał automatycznego odświeżania w sekundach.
     */
    public int $refreshInterval = 60;

    /**
     * Ostatni czas aktualizacji danych.
     */
    public ?string $lastUpdated = null;

    /**
     * Listenery eventów Livewire.
     *
     * @var array
     */
    protected $listeners = [
        'refreshDashboard' => 'refreshData',
        'userUpdated' => 'refreshData',
        'dataChanged' => 'refreshData',
    ];

    /**
     * Inicjalizacja komponentu.
     *
     * Ustawia klucz cache na podstawie nazwy klasy i ID użytkownika,
     * inicjalizuje czas ostatniej aktualizacji.
     */
    public function mount(): void
    {
        $this->initializeCache();
        $this->lastUpdated = now()->diffForHumans();
    }

    /**
     * Inicjalizuje system cache dla komponentu.
     */
    protected function initializeCache(): void
    {
        $className = class_basename(static::class);
        $userId = auth()->id();
        $this->cacheKey = "dashboard.{$className}.{$userId}";
    }

    /**
     * Pobiera dane z cache lub wywołuje metodę getData().
     */
    protected function getCachedData(): Collection|array
    {
        return cache()->remember(
            $this->cacheKey,
            $this->cacheTtl,
            fn () => $this->getData()
        );
    }

    /**
     * Czyści cache komponentu.
     */
    protected function clearCache(): void
    {
        cache()->forget($this->cacheKey);
    }

    /**
     * Odświeża dane komponentu.
     *
     * Czyści cache, ustawia stan ładowania i aktualizuje timestamp.
     */
    public function refreshData(): void
    {
        $this->loading = true;
        $this->clearCache();

        // Symulacja opóźnienia dla lepszego UX
        usleep(500000); // 0.5 sekundy

        $this->loading = false;
        $this->lastUpdated = now()->diffForHumans();

        $this->dispatch('dataRefreshed', [
            'component' => class_basename(static::class),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Formatuje wartość liczbową z separatorami tysięcy.
     */
    protected function formatNumber(int|float $value, int $decimals = 0): string
    {
        return number_format($value, $decimals, ',', ' ');
    }

    /**
     * Formatuje kwotę pieniężną.
     */
    protected function formatCurrency(int|float $amount, string $currency = 'PLN'): string
    {
        return number_format($amount, 2, ',', ' ').' '.$currency;
    }

    /**
     * Formatuje procent.
     */
    protected function formatPercentage(int|float $value, int $decimals = 1): string
    {
        return number_format($value, $decimals, ',', ' ').'%';
    }

    /**
     * Oblicza trend między dwiema wartościami.
     */
    protected function calculateTrend(int|float $current, int|float $previous): array
    {
        if ($previous == 0) {
            return [
                'direction' => $current > 0 ? 'up' : 'neutral',
                'value' => $current > 0 ? '+100%' : '0%',
                'percentage' => $current > 0 ? 100 : 0,
            ];
        }

        $change = (($current - $previous) / $previous) * 100;
        $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral');
        $sign = $change > 0 ? '+' : '';

        return [
            'direction' => $direction,
            'value' => $sign.$this->formatPercentage(abs($change)),
            'percentage' => round($change, 1),
        ];
    }

    /**
     * Pobiera aktualnego użytkownika z cache.
     */
    protected function getUser(): \App\Models\User
    {
        return cache()->remember(
            'user.{auth()->id()}',
            300,
            fn () => auth()->user()->load(['profile', 'pets', 'services'])
        );
    }

    /**
     * Sprawdza czy użytkownik jest właścicielem zwierząt.
     */
    protected function isOwner(): bool
    {
        return $this->getUser()->isOwner();
    }

    /**
     * Sprawdza czy użytkownik jest opiekunem.
     */
    protected function isSitter(): bool
    {
        return $this->getUser()->isSitter();
    }

    /**
     * Loguje błąd komponentu dashboard.
     */
    protected function logError(string $message, array $context = []): void
    {
        logger()->error("Dashboard Component Error: {$message}", array_merge([
            'component' => static::class,
            'user_id' => auth()->id(),
            'cache_key' => $this->cacheKey,
        ], $context));
    }

    /**
     * Abstrakcyjna metoda do implementacji w klasach potomnych.
     *
     * Powinna zwracać dane specyficzne dla danego komponentu.
     */
    abstract protected function getData(): Collection|array;

    /**
     * Zwraca konfigurację Alpine.js dla komponentu.
     */
    protected function getAlpineConfig(): array
    {
        return [
            'loading' => $this->loading,
            'autoRefresh' => $this->autoRefresh,
            'refreshInterval' => $this->refreshInterval,
            'lastUpdated' => $this->lastUpdated,
        ];
    }

    /**
     * Hook wywoływany przed renderowaniem komponentu.
     */
    protected function beforeRender(): void
    {
        // Możliwość nadpisania w klasach potomnych
    }

    /**
     * Hook wywoływany po renderowaniu komponentu.
     */
    protected function afterRender(): void
    {
        // Możliwość nadpisania w klasach potomnych
    }

    /**
     * Renderuje komponent z obsługą błędów.
     */
    public function render(): \Illuminate\View\View
    {
        try {
            $this->beforeRender();

            $view = $this->getView();

            $this->afterRender();

            return $view;
        } catch (\Exception $e) {
            $this->logError('Rendering failed', [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Zwraca widok błędu zamiast crashować całą stronę
            return view('components.dashboard.error', [
                'message' => 'Wystąpił błąd podczas ładowania komponentu.',
            ]);
        }
    }

    /**
     * Zwraca widok komponentu.
     *
     * Domyślnie używa konwencji nazewnictwa na podstawie nazwy klasy.
     */
    protected function getView(): \Illuminate\View\View
    {
        $viewName = 'livewire.dashboard.'.str_replace(['_', '-'], '.', snake_case(class_basename(static::class)));

        return view($viewName);
    }
}
