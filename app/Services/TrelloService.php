<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TrelloService
{
    private string $apiKey;
    private string $token;
    private string $baseUrl = 'https://api.trello.com/1';
    private array $lists;

    public function __construct()
    {
        $this->apiKey = config('trello.api_key');
        $this->token = config('trello.token');
        $this->lists = config('trello.lists', []);
    }

    /**
     * Create the main PetHelp board
     */
    public function createPetHelpBoard(): array
    {
        Log::info('🎯 Creating PetHelp Trello board...');

        $response = Http::post($this->baseUrl . '/boards', [
            'key' => $this->apiKey,
            'token' => $this->token,
            'name' => 'PetHelp - Platforma Opieki nad Zwierzętami',
            'desc' => 'Kompleksowa platforma łącząca właścicieli zwierząt z profesjonalnymi opiekunami. ' .
                     'Obejmuje system rezerwacji, płatności, wyszukiwania i zarządzania usługami.',
            'defaultLists' => false,
            'prefs_background' => 'blue',
            'prefs_permissionLevel' => 'private',
            'prefs_voting' => 'disabled',
            'prefs_comments' => 'members',
            'prefs_invitations' => 'members'
        ]);

        if ($response->successful()) {
            $board = $response->json();
            Log::info('✅ Board created successfully', ['board_id' => $board['id'], 'url' => $board['url']]);
            return $board;
        }

        Log::error('❌ Failed to create Trello board', ['response' => $response->body()]);
        throw new \Exception('Failed to create Trello board: ' . $response->body());
    }

    /**
     * Create project lists (columns)
     */
    public function createProjectLists(string $boardId): array
    {
        $lists = [
            ['name' => '📋 Product Backlog', 'pos' => 1],
            ['name' => '🎯 Sprint Backlog', 'pos' => 2],
            ['name' => '👨‍💻 In Development', 'pos' => 3],
            ['name' => '🧪 Testing', 'pos' => 4],
            ['name' => '📝 Code Review', 'pos' => 5],
            ['name' => '✅ Done', 'pos' => 6],
            ['name' => '🚀 Deployed', 'pos' => 7]
        ];

        $createdLists = [];
        foreach ($lists as $listData) {
            $response = Http::post($this->baseUrl . '/lists', [
                'key' => $this->apiKey,
                'token' => $this->token,
                'name' => $listData['name'],
                'idBoard' => $boardId,
                'pos' => $listData['pos']
            ]);

            if ($response->successful()) {
                $list = $response->json();
                $createdLists[] = $list;
                Log::info('✅ List created', ['name' => $list['name'], 'id' => $list['id']]);
            } else {
                Log::error('❌ Failed to create list', ['name' => $listData['name'], 'response' => $response->body()]);
            }
        }

        return $createdLists;
    }

    /**
     * Create project labels
     */
    public function createProjectLabels(string $boardId): array
    {
        $labels = [
            ['name' => '🔴 Critical', 'color' => 'red'],
            ['name' => '🟡 High Priority', 'color' => 'orange'],
            ['name' => '🟢 Medium Priority', 'color' => 'yellow'],
            ['name' => '🔵 Low Priority', 'color' => 'blue'],
            ['name' => '🏗️ Core System', 'color' => 'purple'],
            ['name' => '🔍 Search & Maps', 'color' => 'green'],
            ['name' => '💰 Payment System', 'color' => 'red'],
            ['name' => '📱 UI/UX', 'color' => 'pink'],
            ['name' => '🐛 Bug Fix', 'color' => 'black'],
            ['name' => '✨ New Feature', 'color' => 'lime'],
            ['name' => '🔧 Refactoring', 'color' => 'sky'],
            ['name' => '📚 Documentation', 'color' => 'blue']
        ];

        $createdLabels = [];
        foreach ($labels as $labelData) {
            $response = Http::post($this->baseUrl . '/labels', [
                'key' => $this->apiKey,
                'token' => $this->token,
                'name' => $labelData['name'],
                'color' => $labelData['color'],
                'idBoard' => $boardId
            ]);

            if ($response->successful()) {
                $label = $response->json();
                $createdLabels[] = $label;
                Log::info('✅ Label created', ['name' => $label['name'], 'color' => $label['color']]);
            }
        }

        return $createdLabels;
    }

    /**
     * Create card from template
     */
    public function createCard(string $listId, array $data, string $template = null): array
    {
        // Use template if provided
        if ($template && isset(config('trello.templates')[$template])) {
            $templateData = config('trello.templates')[$template];
            $cardData = $this->applyTemplate($templateData, $data);
        } else {
            $cardData = $data;
        }

        $payload = [
            'key' => $this->apiKey,
            'token' => $this->token,
            'idList' => $listId,
            'name' => $cardData['name'],
            'desc' => $cardData['description'] ?? '',
        ];

        // Add optional fields
        if (isset($cardData['due_date'])) {
            $payload['due'] = $cardData['due_date'];
        }

        if (isset($cardData['position'])) {
            $payload['pos'] = $cardData['position'];
        }

        $response = Http::post($this->baseUrl . '/cards', $payload);

        if ($response->successful()) {
            $card = $response->json();
            Log::info('✅ Card created', ['name' => $card['name'], 'id' => $card['id']]);

            // Add labels if specified
            if (isset($cardData['labels'])) {
                $this->addLabelsToCard($card['id'], $cardData['labels']);
            }

            return $card;
        }

        Log::error('❌ Failed to create card', ['response' => $response->body()]);
        throw new \Exception('Failed to create Trello card: ' . $response->body());
    }

    /**
     * Move card to different list
     */
    public function moveCard(string $cardId, string $listId): bool
    {
        $response = Http::put($this->baseUrl . "/cards/{$cardId}", [
            'key' => $this->apiKey,
            'token' => $this->token,
            'idList' => $listId
        ]);

        if ($response->successful()) {
            Log::info('✅ Card moved', ['card_id' => $cardId, 'list_id' => $listId]);
            return true;
        }

        Log::error('❌ Failed to move card', ['card_id' => $cardId, 'response' => $response->body()]);
        return false;
    }

    /**
     * Add comment to card
     */
    public function addComment(string $cardId, string $comment): array
    {
        $response = Http::post($this->baseUrl . "/cards/{$cardId}/actions/comments", [
            'key' => $this->apiKey,
            'token' => $this->token,
            'text' => $comment
        ]);

        if ($response->successful()) {
            $commentData = $response->json();
            Log::info('✅ Comment added', ['card_id' => $cardId]);
            return $commentData;
        }

        Log::error('❌ Failed to add comment', ['card_id' => $cardId, 'response' => $response->body()]);
        return [];
    }

    /**
     * Update card progress
     */
    public function updateCardProgress(string $cardId, array $data): bool
    {
        try {
            // Move card based on status
            if (isset($data['status'])) {
                $listId = $this->getListIdForStatus($data['status']);
                if ($listId) {
                    $this->moveCard($cardId, $listId);
                }
            }

            // Add progress comment
            if (isset($data['progress'])) {
                $progressComment = "📊 **Progress Update: {$data['progress']}%**\n";

                if (isset($data['time_spent'])) {
                    $progressComment .= "⏱️ **Time Spent:** {$data['time_spent']}h\n";
                }

                if (isset($data['description'])) {
                    $progressComment .= "📝 **Notes:** {$data['description']}\n";
                }

                $progressComment .= "\n*Updated automatically from Laravel*";

                $this->addComment($cardId, $progressComment);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('❌ Failed to update card progress', ['card_id' => $cardId, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get board statistics
     */
    public function getBoardStats(): array
    {
        $boardId = config('trello.board_id');
        if (!$boardId) {
            return [];
        }

        $cacheKey = "trello_board_stats_{$boardId}";

        return Cache::remember($cacheKey, 300, function () use ($boardId) {
            $response = Http::get($this->baseUrl . "/boards/{$boardId}/lists", [
                'key' => $this->apiKey,
                'token' => $this->token,
                'cards' => 'open'
            ]);

            if (!$response->successful()) {
                return [];
            }

            $lists = $response->json();
            $stats = [];

            foreach ($lists as $list) {
                $listName = strtolower(str_replace(['📋', '🎯', '👨‍💻', '🧪', '📝', '✅', '🚀', ' '], '', $list['name']));
                $stats[$listName] = count($list['cards'] ?? []);
            }

            return $stats;
        });
    }

    /**
     * Import current project tasks
     */
    public function importCurrentTasks(array $lists): void
    {
        $currentTasks = [
            [
                'name' => '✅ Service Creation Form Implementation',
                'list' => 'done',
                'description' => "**Status:** Completed ✅\n**Progress:** 100%\n**Assignee:** Szymon\n\n**Tasks Completed:**\n- [x] Livewire Volt form implementation\n- [x] Dynamic category selection\n- [x] Form validation\n- [x] Database integration\n- [x] Error handling fixed",
                'labels' => ['✨ New Feature', '🏗️ Core System'],
                'completed' => true
            ],
            [
                'name' => '✅ Pet Type Filter Enhancement',
                'list' => 'done',
                'description' => "**Status:** Completed ✅\n**Progress:** 100%\n**Assignee:** Szymon\n\n**Completed:**\n- [x] Fixed UTF8 encoding for emoji icons\n- [x] Active filter highlighting\n- [x] URL parameter handling\n- [x] Alpine.js reactivity fixes",
                'labels' => ['🐛 Bug Fix', '🔍 Search & Maps'],
                'completed' => true
            ],
            [
                'name' => '🔄 Search System Optimization',
                'list' => 'testing',
                'description' => "**Status:** In Testing 🧪\n**Progress:** 85%\n**Assignee:** Szymon\n\n**Tasks:**\n- [x] ServiceSearchService implementation\n- [x] Cache optimization\n- [x] Query performance tuning\n- [ ] Load testing\n- [ ] Performance benchmarks",
                'labels' => ['🔧 Refactoring', '🔍 Search & Maps', '🟡 High Priority']
            ],
            [
                'name' => '💰 PayU Webhook Testing',
                'list' => 'sprintbacklog',
                'description' => "**Status:** Planned 📋\n**Progress:** 0%\n**Assignee:** Szymon\n**Estimated:** 6h\n\n**Tasks:**\n- [ ] Webhook endpoint testing\n- [ ] Payment status handling\n- [ ] Error scenarios\n- [ ] Integration tests\n- [ ] Documentation",
                'labels' => ['🧪 Testing', '💰 Payment System', '🟡 High Priority'],
                'due_date' => '2025-09-30'
            ]
        ];

        foreach ($currentTasks as $taskData) {
            $listId = $this->findListId($lists, $taskData['list']);
            if ($listId) {
                $this->createCard($listId, $taskData);
            }
        }
    }

    /**
     * Apply template to card data
     */
    private function applyTemplate(array $template, array $data): array
    {
        $result = $template;

        // Replace placeholders in name and description
        foreach (['name', 'description'] as $field) {
            if (isset($result[$field])) {
                $result[$field] = $this->replacePlaceholders($result[$field], $data);
            }
        }

        // Merge with provided data
        return array_merge($result, $data);
    }

    /**
     * Replace placeholders in template strings
     */
    private function replacePlaceholders(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $template = str_replace("{{$key}}", $value, $template);
            }
        }
        return $template;
    }

    /**
     * Get list ID for status
     */
    private function getListIdForStatus(string $status): ?string
    {
        $statusMapping = [
            'planned' => $this->lists['backlog'],
            'in_progress' => $this->lists['development'],
            'testing' => $this->lists['testing'],
            'review' => $this->lists['review'],
            'completed' => $this->lists['done'],
            'deployed' => $this->lists['deployed']
        ];

        return $statusMapping[$status] ?? null;
    }

    /**
     * Add labels to card
     */
    private function addLabelsToCard(string $cardId, array $labelNames): void
    {
        // Get board labels
        $boardId = config('trello.board_id');
        $response = Http::get($this->baseUrl . "/boards/{$boardId}/labels", [
            'key' => $this->apiKey,
            'token' => $this->token
        ]);

        if (!$response->successful()) {
            return;
        }

        $boardLabels = $response->json();

        foreach ($labelNames as $labelName) {
            $label = collect($boardLabels)->firstWhere('name', $labelName);
            if ($label) {
                Http::post($this->baseUrl . "/cards/{$cardId}/idLabels", [
                    'key' => $this->apiKey,
                    'token' => $this->token,
                    'value' => $label['id']
                ]);
            }
        }
    }

    /**
     * Find list ID by name pattern
     */
    private function findListId(array $lists, string $pattern): ?string
    {
        foreach ($lists as $list) {
            $listName = strtolower(str_replace([' ', '-', '_'], '', $list['name']));
            if (str_contains($listName, strtolower($pattern))) {
                return $list['id'];
            }
        }
        return null;
    }

    /**
     * Create a webhook for board events
     */
    public function createWebhook(string $modelId, string $callbackUrl, string $description = 'PetHelp Laravel Integration'): array
    {
        Log::info('🔗 Creating Trello webhook', [
            'model_id' => $modelId,
            'callback_url' => $callbackUrl
        ]);

        $response = Http::post($this->baseUrl . '/webhooks', [
            'key' => $this->apiKey,
            'token' => $this->token,
            'callbackURL' => $callbackUrl,
            'idModel' => $modelId,
            'description' => $description
        ]);

        if ($response->successful()) {
            $webhook = $response->json();
            Log::info('✅ Webhook created successfully', ['webhook_id' => $webhook['id']]);
            return $webhook;
        }

        $error = $response->json();
        Log::error('❌ Failed to create webhook', ['error' => $error]);
        throw new \Exception('Failed to create webhook: ' . ($error['message'] ?? 'Unknown error'));
    }

    /**
     * Get all webhooks for the current token
     */
    public function getWebhooks(): array
    {
        Log::info('🔍 Fetching Trello webhooks...');

        $response = Http::get($this->baseUrl . '/tokens/' . $this->token . '/webhooks', [
            'key' => $this->apiKey,
            'token' => $this->token
        ]);

        if ($response->successful()) {
            $webhooks = $response->json();
            Log::info('✅ Webhooks retrieved', ['count' => count($webhooks)]);
            return $webhooks;
        }

        $error = $response->json();
        Log::error('❌ Failed to get webhooks', ['error' => $error]);
        throw new \Exception('Failed to get webhooks: ' . ($error['message'] ?? 'Unknown error'));
    }

    /**
     * Delete a webhook
     */
    public function deleteWebhook(string $webhookId): bool
    {
        Log::info('🗑️ Deleting Trello webhook', ['webhook_id' => $webhookId]);

        $response = Http::delete($this->baseUrl . "/webhooks/{$webhookId}", [
            'key' => $this->apiKey,
            'token' => $this->token
        ]);

        if ($response->successful()) {
            Log::info('✅ Webhook deleted successfully');
            return true;
        }

        $error = $response->json();
        Log::error('❌ Failed to delete webhook', ['error' => $error]);
        throw new \Exception('Failed to delete webhook: ' . ($error['message'] ?? 'Unknown error'));
    }
}