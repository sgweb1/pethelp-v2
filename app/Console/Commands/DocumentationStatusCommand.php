<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DocumentationStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:status {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate documentation status report showing coverage and missing docs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📊 Generating documentation status report...');
        $this->newLine();

        // Collect documentation statistics
        $stats = $this->collectDocumentationStats();

        if ($this->option('format') === 'json') {
            $this->line(json_encode($stats, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }

        // Display coverage table
        $this->displayCoverageTable($stats);
        $this->newLine();

        // Display missing documentation
        $this->displayMissingDocs($stats);
        $this->newLine();

        // Display recommendations
        $this->displayRecommendations($stats);

        return Command::SUCCESS;
    }

    private function collectDocumentationStats(): array
    {
        // API Controllers
        $apiControllers = $this->scanDirectory('app/Http/Controllers/Api', '*.php');
        $apiDocs = $this->scanDirectory('docs/dev/reference/api', '*.md');

        // Livewire Components
        $livewireComponents = $this->scanDirectory('app/Livewire', '*.php');
        $componentDocs = $this->scanDirectory('docs/dev/reference/components', '*.md');

        // Models
        $models = $this->scanDirectory('app/Models', '*.php');
        $modelDocs = $this->scanDirectory('docs/dev/reference/models', '*.md');

        // Services
        $services = $this->scanDirectory('app/Services', '*.php');
        $serviceDocs = $this->scanDirectory('docs/dev/reference/services', '*.md');

        return [
            'api' => [
                'total' => count($apiControllers),
                'documented' => count($apiDocs),
                'coverage' => $this->calculateCoverage(count($apiDocs), count($apiControllers)),
                'missing' => $this->findMissingDocs($apiControllers, $apiDocs, 'api')
            ],
            'components' => [
                'total' => count($livewireComponents),
                'documented' => count($componentDocs),
                'coverage' => $this->calculateCoverage(count($componentDocs), count($livewireComponents)),
                'missing' => $this->findMissingDocs($livewireComponents, $componentDocs, 'components')
            ],
            'models' => [
                'total' => count($models),
                'documented' => count($modelDocs),
                'coverage' => $this->calculateCoverage(count($modelDocs), count($models)),
                'missing' => $this->findMissingDocs($models, $modelDocs, 'models')
            ],
            'services' => [
                'total' => count($services),
                'documented' => count($serviceDocs),
                'coverage' => $this->calculateCoverage(count($serviceDocs), count($services)),
                'missing' => $this->findMissingDocs($services, $serviceDocs, 'services')
            ]
        ];
    }

    private function scanDirectory(string $path, string $pattern): array
    {
        if (!File::exists($path)) {
            return [];
        }

        return collect(File::glob($path . '/' . $pattern))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME))
            ->values()
            ->toArray();
    }

    private function calculateCoverage(int $documented, int $total): float
    {
        return $total > 0 ? round(($documented / $total) * 100, 1) : 0;
    }

    private function findMissingDocs(array $sourceFiles, array $docFiles, string $type): array
    {
        return array_diff($sourceFiles, $docFiles);
    }

    private function displayCoverageTable(array $stats): void
    {
        $this->info('📈 Documentation Coverage Overview');

        $rows = [];
        $totalFiles = 0;
        $totalDocumented = 0;

        foreach ($stats as $category => $data) {
            $status = $this->getCoverageStatus($data['coverage']);
            $rows[] = [
                ucfirst($category),
                $data['documented'] . '/' . $data['total'],
                $data['coverage'] . '%',
                $status
            ];
            $totalFiles += $data['total'];
            $totalDocumented += $data['documented'];
        }

        $overallCoverage = $this->calculateCoverage($totalDocumented, $totalFiles);
        $rows[] = ['---', '---', '---', '---'];
        $rows[] = [
            '<comment>OVERALL</comment>',
            "<comment>$totalDocumented/$totalFiles</comment>",
            "<comment>{$overallCoverage}%</comment>",
            '<comment>' . $this->getCoverageStatus($overallCoverage) . '</comment>'
        ];

        $this->table(
            ['Category', 'Documented', 'Coverage', 'Status'],
            $rows
        );
    }

    private function getCoverageStatus(float $coverage): string
    {
        if ($coverage >= 80) return '🟢 Good';
        if ($coverage >= 60) return '🟡 Partial';
        if ($coverage >= 40) return '🟠 Poor';
        return '🔴 Critical';
    }

    private function displayMissingDocs(array $stats): void
    {
        $this->info('❌ Missing Documentation');

        $hasMissing = false;
        foreach ($stats as $category => $data) {
            if (!empty($data['missing'])) {
                $hasMissing = true;
                $this->warn(ucfirst($category) . ' (' . count($data['missing']) . ' missing):');
                foreach ($data['missing'] as $missing) {
                    $this->line("  📁 $missing");
                }
                $this->newLine();
            }
        }

        if (!$hasMissing) {
            $this->info('✅ All files have corresponding documentation!');
        }
    }

    private function displayRecommendations(array $stats): void
    {
        $this->info('💡 Recommendations');

        $recommendations = [];

        foreach ($stats as $category => $data) {
            if ($data['coverage'] < 50) {
                $recommendations[] = "🔴 Priority: Document {$category} (only {$data['coverage']}% coverage)";
            } elseif ($data['coverage'] < 80) {
                $recommendations[] = "🟡 Improve: {$category} documentation ({$data['coverage']}% coverage)";
            }
        }

        if (empty($recommendations)) {
            $this->info('✅ Documentation coverage is good across all categories!');
        } else {
            foreach ($recommendations as $rec) {
                $this->line($rec);
            }
        }

        $this->newLine();
        $this->info('🤖 Run `php artisan docs:generate --missing` to auto-generate missing docs');
        $this->info('🔍 Run `./docs-monitor.sh` to monitor changes requiring doc updates');
    }
}
