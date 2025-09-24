<?php

namespace App\Console\Commands;

use App\Services\TrelloService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TrelloWebhookCommand extends Command
{
    protected $signature = 'trello:webhook {action : create, delete, or list} {--url= : Webhook URL (for create action)}';
    protected $description = 'Manage Trello webhooks for the project board';

    private TrelloService $trello;

    public function __construct(TrelloService $trello)
    {
        parent::__construct();
        $this->trello = $trello;
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        if (!config('trello.api_key') || !config('trello.token')) {
            $this->error('❌ Trello API credentials not configured. Run trello:setup first.');
            return 1;
        }

        switch ($action) {
            case 'create':
                return $this->createWebhook();
            case 'delete':
                return $this->deleteWebhooks();
            case 'list':
                return $this->listWebhooks();
            default:
                $this->error('❌ Invalid action. Use: create, delete, or list');
                return 1;
        }
    }

    private function createWebhook(): int
    {
        $boardId = config('trello.board_id');
        if (!$boardId) {
            $this->error('❌ Board ID not configured. Run trello:setup first.');
            return 1;
        }

        $webhookUrl = $this->option('url') ?? $this->ask('Enter webhook URL (e.g., https://your-domain.com/api/trello/webhook)');

        if (!$webhookUrl) {
            $this->error('❌ Webhook URL is required');
            return 1;
        }

        try {
            $this->info('🔗 Creating Trello webhook...');

            $webhook = $this->trello->createWebhook($boardId, $webhookUrl);

            $this->info('✅ Webhook created successfully!');
            $this->table(['Property', 'Value'], [
                ['ID', $webhook['id']],
                ['URL', $webhook['callbackURL']],
                ['Model ID', $webhook['idModel']],
                ['Active', $webhook['active'] ? 'Yes' : 'No']
            ]);

            // Optionally store webhook ID in config or database
            $this->info('💡 Consider storing webhook ID: ' . $webhook['id']);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Failed to create webhook: ' . $e->getMessage());
            Log::error('Trello webhook creation failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    private function deleteWebhooks(): int
    {
        try {
            $this->info('🔍 Fetching existing webhooks...');

            $webhooks = $this->trello->getWebhooks();

            if (empty($webhooks)) {
                $this->info('ℹ️ No webhooks found');
                return 0;
            }

            $this->table(
                ['ID', 'URL', 'Model ID', 'Active'],
                array_map(function ($webhook) {
                    return [
                        substr($webhook['id'], 0, 8) . '...',
                        $webhook['callbackURL'],
                        substr($webhook['idModel'], 0, 8) . '...',
                        $webhook['active'] ? 'Yes' : 'No'
                    ];
                }, $webhooks)
            );

            if (!$this->confirm('Delete all webhooks?')) {
                $this->info('❌ Operation cancelled');
                return 0;
            }

            foreach ($webhooks as $webhook) {
                $this->info("🗑️ Deleting webhook: {$webhook['id']}");
                $this->trello->deleteWebhook($webhook['id']);
            }

            $this->info('✅ All webhooks deleted successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Failed to delete webhooks: ' . $e->getMessage());
            Log::error('Trello webhook deletion failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    private function listWebhooks(): int
    {
        try {
            $this->info('🔍 Fetching existing webhooks...');

            $webhooks = $this->trello->getWebhooks();

            if (empty($webhooks)) {
                $this->info('ℹ️ No webhooks configured');
                return 0;
            }

            $this->info('📋 Current webhooks:');
            $this->table(
                ['ID', 'URL', 'Model ID', 'Active', 'Description'],
                array_map(function ($webhook) {
                    return [
                        substr($webhook['id'], 0, 12) . '...',
                        $webhook['callbackURL'],
                        substr($webhook['idModel'], 0, 8) . '...',
                        $webhook['active'] ? '✅ Active' : '❌ Inactive',
                        $webhook['description'] ?? 'No description'
                    ];
                }, $webhooks)
            );

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Failed to list webhooks: ' . $e->getMessage());
            Log::error('Trello webhook listing failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
}