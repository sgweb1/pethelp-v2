<?php

namespace App\Console\Commands;

use App\Models\Advertisement;
use App\Models\Event;
use App\Models\MapItem;
use App\Models\ProfessionalService;
use Illuminate\Console\Command;

class SyncMapData extends Command
{
    protected $signature = 'map:sync
                           {--model= : Specific model to sync (advertisements, events, services)}
                           {--force : Force sync even if map data exists}
                           {--chunk=100 : Number of records to process at once}';

    protected $description = 'Synchronize existing data to unified map system';

    public function handle(): int
    {
        $this->info('🗺️  Rozpoczynam synchronizację danych mapy...');

        $model = $this->option('model');
        $force = $this->option('force');
        $chunkSize = (int) $this->option('chunk');

        try {
            if (! $model || $model === 'advertisements') {
                $this->syncAdvertisements($force, $chunkSize);
            }

            if (! $model || $model === 'events') {
                $this->syncEvents($force, $chunkSize);
            }

            if (! $model || $model === 'services') {
                $this->syncProfessionalServices($force, $chunkSize);
            }

            $this->info('✅ Synchronizacja zakończona pomyślnie!');
            $this->displayStats();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Błąd podczas synchronizacji: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    private function syncAdvertisements(bool $force, int $chunkSize): void
    {
        $this->info('📢 Synchronizacja ogłoszeń...');

        $query = Advertisement::query()
            ->whereNotNull(['latitude', 'longitude'])
            ->where('status', 'published');

        if (! $force) {
            $query->whereDoesntHave('mapItem');
        }

        $total = $query->count();
        $this->info("Znaleziono {$total} ogłoszeń do synchronizacji");

        if ($total === 0) {
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $synced = 0;
        $errors = 0;

        $query->chunk($chunkSize, function ($advertisements) use ($bar, &$synced, &$errors, $force) {
            foreach ($advertisements as $advertisement) {
                try {
                    if ($force) {
                        $advertisement->mapItem()?->delete();
                    }

                    $advertisement->syncToMap();
                    $synced++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Błąd synchronizacji ogłoszenia #{$advertisement->id}: ".$e->getMessage());
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("📢 Zsynchronizowano {$synced} ogłoszeń (błędów: {$errors})");
    }

    private function syncEvents(bool $force, int $chunkSize): void
    {
        $this->info('📅 Synchronizacja eventów...');

        if (! class_exists(Event::class)) {
            $this->warn('⚠️  Model Event nie istnieje - pomijam synchronizację eventów');

            return;
        }

        $query = Event::query()
            ->with('location')
            ->whereHas('location', function ($q) {
                $q->whereNotNull(['latitude', 'longitude']);
            })
            ->where('status', 'published');

        if (! $force) {
            $query->whereDoesntHave('mapItem');
        }

        $total = $query->count();
        $this->info("Znaleziono {$total} eventów do synchronizacji");

        if ($total === 0) {
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $synced = 0;
        $errors = 0;

        $query->chunk($chunkSize, function ($events) use ($bar, &$synced, &$errors, $force) {
            foreach ($events as $event) {
                try {
                    if ($force) {
                        $event->mapItem()?->delete();
                    }

                    $event->syncToMap();
                    $synced++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Błąd synchronizacji eventu #{$event->id}: ".$e->getMessage());
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("📅 Zsynchronizowano {$synced} eventów (błędów: {$errors})");
    }

    private function syncProfessionalServices(bool $force, int $chunkSize): void
    {
        $this->info('🏢 Synchronizacja usług profesjonalnych...');

        $query = ProfessionalService::query()
            ->whereNotNull(['latitude', 'longitude'])
            ->where('status', 'published');

        if (! $force) {
            $query->whereDoesntHave('mapItem');
        }

        $total = $query->count();
        $this->info("Znaleziono {$total} usług do synchronizacji");

        if ($total === 0) {
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $synced = 0;
        $errors = 0;

        $query->chunk($chunkSize, function ($services) use ($bar, &$synced, &$errors, $force) {
            foreach ($services as $service) {
                try {
                    if ($force) {
                        $service->mapItem()?->delete();
                    }

                    $service->syncToMap();
                    $synced++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Błąd synchronizacji usługi #{$service->id}: ".$e->getMessage());
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("🏢 Zsynchronizowano {$synced} usług (błędów: {$errors})");
    }

    private function displayStats(): void
    {
        $this->newLine();
        $this->info('📊 Statystyki mapy:');

        $stats = MapItem::selectRaw('content_type, COUNT(*) as count')
            ->groupBy('content_type')
            ->pluck('count', 'content_type')
            ->toArray();

        foreach ($stats as $type => $count) {
            $icon = match ($type) {
                'event' => '📅',
                'adoption' => '❤️',
                'sale' => '💰',
                'lost_pet' => '🚨',
                'found_pet' => '✅',
                'supplies' => '🛍️',
                'service' => '🏢',
                default => '📍'
            };

            $this->line("  {$icon} {$type}: {$count}");
        }

        $total = array_sum($stats);
        $this->info("📍 Łączna liczba lokalizacji: {$total}");
    }
}
