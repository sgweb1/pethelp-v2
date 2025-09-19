<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class LogsDashboard extends Component
{
    public string $selectedDate;

    public array $logs = [];

    public array $filteredLogs = [];

    public string $filterType = 'all';

    public string $searchQuery = '';

    public bool $autoRefresh = false;

    public function mount(): void
    {
        $this->selectedDate = date('Y-m-d');
        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        try {
            $response = Http::get('/api/js-logs', ['date' => $this->selectedDate]);

            if ($response->successful()) {
                $this->logs = $response->json('logs', []);
            } else {
                $this->logs = $this->loadLogsFromFile();
            }

            $this->applyFilters();
        } catch (\Exception $e) {
            $this->logs = $this->loadLogsFromFile();
            $this->applyFilters();
        }
    }

    private function loadLogsFromFile(): array
    {
        $filename = 'logs/js-errors-'.$this->selectedDate.'.log';

        if (! Storage::disk('local')->exists($filename)) {
            return [];
        }

        $content = Storage::disk('local')->get($filename);
        $lines = array_filter(explode("\n", $content));

        return array_map(function ($line) {
            return json_decode($line, true);
        }, array_reverse($lines)); // Najnowsze na górze
    }

    public function applyFilters(): void
    {
        $logs = $this->logs;

        // Filtruj po typie
        if ($this->filterType !== 'all') {
            $logs = array_filter($logs, fn ($log) => $log['type'] === $this->filterType);
        }

        // Filtruj po zapytaniu
        if (! empty($this->searchQuery)) {
            $query = strtolower($this->searchQuery);
            $logs = array_filter($logs, function ($log) use ($query) {
                return str_contains(strtolower($log['message']), $query) ||
                       str_contains(strtolower($log['url']), $query) ||
                       str_contains(strtolower($log['filename'] ?? ''), $query);
            });
        }

        $this->filteredLogs = array_values($logs);
    }

    public function updatedFilterType(): void
    {
        $this->applyFilters();
    }

    public function updatedSearchQuery(): void
    {
        $this->applyFilters();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadLogs();
    }

    public function clearLogs(): void
    {
        $filename = 'logs/js-errors-'.$this->selectedDate.'.log';

        if (Storage::disk('local')->exists($filename)) {
            Storage::disk('local')->delete($filename);
            $this->logs = [];
            $this->filteredLogs = [];
        }
    }

    public function downloadLogs(): void
    {
        $filename = 'logs/js-errors-'.$this->selectedDate.'.log';

        if (Storage::disk('local')->exists($filename)) {
            return Storage::disk('local')->download($filename);
        }
    }

    #[On('log-received')]
    public function refreshLogs(): void
    {
        if ($this->autoRefresh) {
            $this->loadLogs();
        }
    }

    public function getLogTypes(): array
    {
        $types = array_unique(array_column($this->logs, 'type'));

        return array_combine($types, $types);
    }

    public function getLogStats(): array
    {
        return [
            'total' => count($this->logs),
            'errors' => count(array_filter($this->logs, fn ($log) => in_array($log['type'], ['javascript_error', 'console_error']))),
            'warnings' => count(array_filter($this->logs, fn ($log) => $log['type'] === 'console_warn')),
            'sessions' => count(array_unique(array_column($this->logs, 'session_id'))),
        ];
    }

    public function render()
    {
        return view('livewire.logs-dashboard', [
            'logTypes' => $this->getLogTypes(),
            'stats' => $this->getLogStats(),
        ]);
    }
}
