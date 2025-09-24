<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\TrelloService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TrelloWebhookController extends Controller
{
    private TrelloService $trello;

    public function __construct(TrelloService $trello)
    {
        $this->trello = $trello;
    }

    /**
     * Handle Trello webhook callbacks
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        // Verify webhook source
        if (!$this->verifyWebhookSignature($request)) {
            Log::warning('🚨 Invalid Trello webhook signature', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all()
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $payload = $request->all();

            Log::info('📨 Trello webhook received', [
                'type' => $payload['action']['type'] ?? 'unknown',
                'model' => $payload['model']['name'] ?? 'N/A'
            ]);

            // Handle different webhook events
            $actionType = $payload['action']['type'] ?? '';

            switch ($actionType) {
                case 'updateCard':
                    return $this->handleCardUpdate($payload);

                case 'createCard':
                    return $this->handleCardCreated($payload);

                case 'deleteCard':
                    return $this->handleCardDeleted($payload);

                case 'moveCard':
                    return $this->handleCardMoved($payload);

                case 'commentCard':
                    return $this->handleCardComment($payload);

                default:
                    Log::info('ℹ️ Unhandled Trello webhook type', ['type' => $actionType]);
                    return response()->json(['status' => 'ignored']);
            }

        } catch (\Exception $e) {
            Log::error('❌ Trello webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle HEAD requests for webhook verification
     */
    public function verifyWebhook(Request $request): JsonResponse
    {
        Log::info('🔍 Trello webhook verification requested');
        return response()->json(['status' => 'verified']);
    }

    /**
     * Handle card update events
     */
    private function handleCardUpdate(array $payload): JsonResponse
    {
        $card = $payload['action']['data']['card'] ?? [];
        $cardId = $card['id'] ?? null;

        if (!$cardId) {
            return response()->json(['status' => 'no_card_id']);
        }

        // Find related service
        $service = Service::where('trello_card_id', $cardId)->first();
        if (!$service) {
            Log::info('ℹ️ Card update for non-service card', ['card_id' => $cardId]);
            return response()->json(['status' => 'not_service_card']);
        }

        // Clear cache for board stats
        $this->clearTrelloBoardCache();

        // Log the update
        Log::info('✅ Service-related card updated', [
            'service_id' => $service->id,
            'card_id' => $cardId,
            'card_name' => $card['name'] ?? 'Unknown'
        ]);

        return response()->json(['status' => 'processed', 'service_id' => $service->id]);
    }

    /**
     * Handle card creation events
     */
    private function handleCardCreated(array $payload): JsonResponse
    {
        $card = $payload['action']['data']['card'] ?? [];
        $list = $payload['action']['data']['list'] ?? [];

        Log::info('🎉 New Trello card created', [
            'card_name' => $card['name'] ?? 'Unknown',
            'list_name' => $list['name'] ?? 'Unknown'
        ]);

        $this->clearTrelloBoardCache();

        return response()->json(['status' => 'logged']);
    }

    /**
     * Handle card deletion events
     */
    private function handleCardDeleted(array $payload): JsonResponse
    {
        $card = $payload['action']['data']['card'] ?? [];
        $cardId = $card['id'] ?? null;

        if ($cardId) {
            // Find and update related service
            $service = Service::where('trello_card_id', $cardId)->first();
            if ($service) {
                // Clear the Trello card ID since card was deleted
                $service->update(['trello_card_id' => null]);

                Log::info('🗑️ Service Trello card deleted', [
                    'service_id' => $service->id,
                    'card_id' => $cardId
                ]);
            }
        }

        $this->clearTrelloBoardCache();

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle card move events
     */
    private function handleCardMoved(array $payload): JsonResponse
    {
        $card = $payload['action']['data']['card'] ?? [];
        $listBefore = $payload['action']['data']['listBefore'] ?? [];
        $listAfter = $payload['action']['data']['listAfter'] ?? [];

        $cardId = $card['id'] ?? null;

        if (!$cardId) {
            return response()->json(['status' => 'no_card_id']);
        }

        // Check if this is a service-related card
        $service = Service::where('trello_card_id', $cardId)->first();

        Log::info('🔄 Card moved between lists', [
            'card_name' => $card['name'] ?? 'Unknown',
            'from_list' => $listBefore['name'] ?? 'Unknown',
            'to_list' => $listAfter['name'] ?? 'Unknown',
            'is_service_card' => $service ? 'yes' : 'no'
        ]);

        // Handle service activation based on list movement
        if ($service) {
            $this->handleServiceStatusFromList($service, $listAfter['name'] ?? '');
        }

        $this->clearTrelloBoardCache();

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle card comment events
     */
    private function handleCardComment(array $payload): JsonResponse
    {
        $card = $payload['action']['data']['card'] ?? [];
        $text = $payload['action']['data']['text'] ?? '';
        $memberCreator = $payload['action']['memberCreator'] ?? [];

        Log::info('💬 Comment added to Trello card', [
            'card_name' => $card['name'] ?? 'Unknown',
            'author' => $memberCreator['fullName'] ?? 'Unknown',
            'comment_length' => strlen($text)
        ]);

        $this->clearTrelloBoardCache();

        return response()->json(['status' => 'logged']);
    }

    /**
     * Handle service status based on Trello list
     */
    private function handleServiceStatusFromList(Service $service, string $listName): void
    {
        $activationLists = ['Done', 'Deployed', 'Testing', 'Live'];
        $deactivationLists = ['Product Backlog', 'Sprint Backlog', 'In Development'];

        $shouldBeActive = false;
        foreach ($activationLists as $activeList) {
            if (str_contains($listName, $activeList)) {
                $shouldBeActive = true;
                break;
            }
        }

        // Only update if status should change
        if ($service->is_active !== $shouldBeActive) {
            $service->update(['is_active' => $shouldBeActive]);

            Log::info('🔄 Service status updated from Trello', [
                'service_id' => $service->id,
                'list_name' => $listName,
                'new_status' => $shouldBeActive ? 'active' : 'inactive'
            ]);
        }
    }

    /**
     * Verify webhook signature from Trello
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        // In production, you should verify the webhook signature
        // For now, we'll do basic verification
        $webhookSecret = config('trello.webhook_secret');

        if (!$webhookSecret) {
            // If no secret configured, allow all requests in development
            return config('app.debug', false);
        }

        // Implement proper signature verification here
        // This is a simplified version
        $signature = $request->header('X-Trello-Webhook');
        return !empty($signature);
    }

    /**
     * Clear Trello board cache to refresh dashboard data
     */
    private function clearTrelloBoardCache(): void
    {
        $boardId = config('trello.board_id');
        if ($boardId) {
            Cache::forget("trello_board_stats_{$boardId}");
            Cache::forget("trello_recent_activity_{$boardId}");
        }
    }
}