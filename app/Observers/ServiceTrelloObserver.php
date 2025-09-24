<?php

namespace App\Observers;

use App\Models\Service;
use App\Services\TrelloService;
use Illuminate\Support\Facades\Log;

class ServiceTrelloObserver
{
    private TrelloService $trello;

    public function __construct(TrelloService $trello)
    {
        $this->trello = $trello;
    }

    /**
     * Handle the Service "created" event.
     */
    public function created(Service $service): void
    {
        if (!config('trello.automation.auto_create_cards', true)) {
            return;
        }

        try {
            Log::info('🎯 Creating Trello card for new service', ['service_id' => $service->id]);

            $cardData = [
                'name' => "✨ New Service: {$service->title}",
                'description' => $this->buildServiceDescription($service),
                'labels' => ['✨ New Feature', '🏗️ Core System']
            ];

            // Create card in backlog
            $backlogListId = config('trello.lists.backlog');
            if ($backlogListId) {
                $card = $this->trello->createCard($backlogListId, $cardData, 'service_created');

                // Store Trello card ID in service
                $service->update(['trello_card_id' => $card['id']]);

                Log::info('✅ Trello card created for service', [
                    'service_id' => $service->id,
                    'card_id' => $card['id'],
                    'card_url' => $card['url'] ?? null
                ]);
            } else {
                Log::warning('⚠️ Backlog list ID not configured for Trello');
            }

        } catch (\Exception $e) {
            Log::error('❌ Failed to create Trello card for service', [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Service "updated" event.
     */
    public function updated(Service $service): void
    {
        if (!$service->trello_card_id || !config('trello.automation.auto_move_on_status', true)) {
            return;
        }

        try {
            // Track important changes
            $changes = [];

            if ($service->isDirty('is_active')) {
                $changes['activation'] = $service->is_active;
            }

            if ($service->isDirty('title')) {
                $changes['title'] = ['from' => $service->getOriginal('title'), 'to' => $service->title];
            }

            if ($service->isDirty('price_per_hour') || $service->isDirty('price_per_day')) {
                $changes['pricing'] = true;
            }

            if (empty($changes)) {
                return; // No important changes
            }

            $this->handleServiceChanges($service, $changes);

        } catch (\Exception $e) {
            Log::error('❌ Failed to update Trello card for service', [
                'service_id' => $service->id,
                'card_id' => $service->trello_card_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Service "deleted" event.
     */
    public function deleted(Service $service): void
    {
        if (!$service->trello_card_id) {
            return;
        }

        try {
            // Add final comment and archive card
            $comment = "🗑️ **Service Deleted**\n" .
                      "Service '{$service->title}' has been deleted from the system.\n\n" .
                      "*Card will be archived automatically.*";

            $this->trello->addComment($service->trello_card_id, $comment);

            // Move to a "Deleted" list or add a label
            $deletedComment = "⚠️ This card represents a deleted service and should be archived.";
            $this->trello->addComment($service->trello_card_id, $deletedComment);

            Log::info('✅ Trello card updated for deleted service', [
                'service_id' => $service->id,
                'card_id' => $service->trello_card_id
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to update Trello card for deleted service', [
                'service_id' => $service->id,
                'card_id' => $service->trello_card_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Build comprehensive service description for Trello card
     */
    private function buildServiceDescription(Service $service): string
    {
        $description = "**🐾 New Service Created**\n\n";

        // Basic service info
        $description .= "**Service Details:**\n";
        $description .= "• **Title:** {$service->title}\n";
        $description .= "• **Category:** {$service->category->name}\n";
        $description .= "• **Sitter:** {$service->sitter->name}\n";

        // Pricing info
        if ($service->price_per_hour || $service->price_per_day) {
            $description .= "• **Price:** {$service->display_price}\n";
        }

        // Pet details
        if ($service->pet_types) {
            $description .= "• **Pet Types:** " . implode(', ', $service->pet_types) . "\n";
        }

        if ($service->pet_sizes) {
            $description .= "• **Pet Sizes:** " . implode(', ', $service->pet_sizes) . "\n";
        }

        // Service types
        $serviceTypes = [];
        if ($service->home_service) $serviceTypes[] = "🏡 At client's home";
        if ($service->sitter_home) $serviceTypes[] = "🏠 At sitter's home";
        if (!empty($serviceTypes)) {
            $description .= "• **Service Types:** " . implode(', ', $serviceTypes) . "\n";
        }

        // Max pets
        if ($service->max_pets) {
            $description .= "• **Max Pets:** {$service->max_pets}\n";
        }

        $description .= "\n**📋 Review Checklist:**\n";
        $description .= "- [ ] Verify service details are accurate\n";
        $description .= "- [ ] Check pricing is reasonable\n";
        $description .= "- [ ] Validate category assignment\n";
        $description .= "- [ ] Review sitter profile completeness\n";
        $description .= "- [ ] Approve for publication\n";

        $description .= "\n**🔗 Quick Links:**\n";
        $description .= "• Service ID: #{$service->id}\n";
        $description .= "• Created: " . $service->created_at->format('Y-m-d H:i') . "\n";

        if ($service->description) {
            $description .= "\n**📝 Service Description:**\n";
            $description .= substr($service->description, 0, 300);
            if (strlen($service->description) > 300) {
                $description .= "...\n";
            }
        }

        return $description;
    }

    /**
     * Handle specific service changes
     */
    private function handleServiceChanges(Service $service, array $changes): void
    {
        $comments = [];

        // Handle activation/deactivation
        if (isset($changes['activation'])) {
            if ($changes['activation']) {
                // Service activated - move to testing or done
                $testingListId = config('trello.lists.testing');
                if ($testingListId) {
                    $this->trello->moveCard($service->trello_card_id, $testingListId);
                }

                $comments[] = "✅ **Service Activated**\n" .
                            "Service is now live and visible to users!\n" .
                            "• Status: Published\n" .
                            "• Visibility: Public\n" .
                            "• Available for booking: Yes";
            } else {
                // Service deactivated - move back to development
                $devListId = config('trello.lists.development');
                if ($devListId) {
                    $this->trello->moveCard($service->trello_card_id, $devListId);
                }

                $comments[] = "⏸️ **Service Deactivated**\n" .
                            "Service has been temporarily disabled.\n" .
                            "• Status: Draft\n" .
                            "• Visibility: Hidden\n" .
                            "• Available for booking: No";
            }
        }

        // Handle title changes
        if (isset($changes['title'])) {
            $comments[] = "✏️ **Title Updated**\n" .
                        "• From: '{$changes['title']['from']}'\n" .
                        "• To: '{$changes['title']['to']}'";
        }

        // Handle pricing changes
        if (isset($changes['pricing'])) {
            $comments[] = "💰 **Pricing Updated**\n" .
                        "• New price: {$service->display_price}\n" .
                        "• Per hour: " . ($service->price_per_hour ? $service->price_per_hour . " PLN" : "Not set") . "\n" .
                        "• Per day: " . ($service->price_per_day ? $service->price_per_day . " PLN" : "Not set");
        }

        // Add all comments
        foreach ($comments as $comment) {
            $comment .= "\n\n*Updated automatically from Laravel at " . now()->format('Y-m-d H:i:s') . "*";
            $this->trello->addComment($service->trello_card_id, $comment);
        }

        Log::info('✅ Trello card updated for service changes', [
            'service_id' => $service->id,
            'card_id' => $service->trello_card_id,
            'changes' => array_keys($changes),
            'comments_added' => count($comments)
        ]);
    }
}