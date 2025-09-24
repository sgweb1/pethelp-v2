<?php

namespace App\Livewire\Dashboard;

use App\Services\TrelloService;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrelloWidget extends Component
{
    public array $stats = [];
    public array $recentActivity = [];
    public bool $isLoading = false;
    public ?string $boardUrl = null;
    public bool $isConfigured = false;

    public function mount(): void
    {
        $this->boardUrl = config('trello.board_url');
        $this->isConfigured = config('trello.api_key') && config('trello.token') && config('trello.board_id');

        if ($this->isConfigured) {
            $this->loadTrelloData();
        }
    }

    public function loadTrelloData(): void
    {
        if (!$this->isConfigured) {
            return;
        }

        $this->isLoading = true;

        try {
            $trello = app(TrelloService::class);

            // Load stats from TrelloService
            $this->stats = $trello->getBoardStats();

            // Load recent activity (simplified for now)
            $this->recentActivity = $this->getRecentActivity();

        } catch (\Exception $e) {
            Log::error('Failed to load Trello data', ['error' => $e->getMessage()]);
            session()->flash('trello_error', 'Failed to load Trello data: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function refreshData(): void
    {
        // Clear cache and reload
        $boardId = config('trello.board_id');
        if ($boardId) {
            Cache::forget("trello_board_stats_{$boardId}");
        }

        $this->loadTrelloData();

        $this->dispatch('trello-refreshed');
        session()->flash('trello_success', 'Trello data refreshed successfully!');
    }

    private function getRecentActivity(): array
    {
        // This would typically fetch recent card activities from Trello API
        // For now, we'll return static data that represents recent project activity
        return [
            [
                'type' => 'card_created',
                'title' => '✅ Pet Type Filter Enhancement',
                'list' => 'Done',
                'time' => '2 hours ago',
                'author' => 'Szymon'
            ],
            [
                'type' => 'card_moved',
                'title' => '🔄 Search System Optimization',
                'list' => 'Testing',
                'time' => '4 hours ago',
                'author' => 'Szymon'
            ],
            [
                'type' => 'card_updated',
                'title' => '💰 PayU Webhook Testing',
                'list' => 'Sprint Backlog',
                'time' => '1 day ago',
                'author' => 'Szymon'
            ]
        ];
    }

    public function getTotalTasksProperty(): int
    {
        return array_sum($this->stats);
    }

    public function getProgressPercentageProperty(): int
    {
        $total = $this->getTotalTasksProperty();
        if ($total === 0) {
            return 0;
        }

        $completed = ($this->stats['done'] ?? 0) + ($this->stats['deployed'] ?? 0);
        return round(($completed / $total) * 100);
    }

    public function render()
    {
        return view('livewire.dashboard.trello-widget');
    }
}