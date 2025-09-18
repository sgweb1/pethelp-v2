<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Message;
use Illuminate\Support\Collection;

class NotificationService
{
    public function createNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = [],
        bool $isImportant = false
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_important' => $isImportant,
        ]);
    }

    public function notifyBookingCreated(Booking $booking): void
    {
        $this->createNotification(
            $booking->sitter,
            'booking_created',
            'Nowa rezerwacja!',
            "Otrzymałeś nową rezerwację na usługę \"{$booking->service->title}\" od {$booking->owner->name}.",
            [
                'booking_id' => $booking->id,
                'owner_name' => $booking->owner->name,
                'service_title' => $booking->service->title,
                'start_date' => $booking->start_date->format('d.m.Y H:i')
            ],
            true
        );
    }

    public function notifyBookingConfirmed(Booking $booking): void
    {
        $this->createNotification(
            $booking->owner,
            'booking_confirmed',
            'Rezerwacja potwierdzona!',
            "{$booking->sitter->name} potwierdził Twoją rezerwację na \"{$booking->service->title}\".",
            [
                'booking_id' => $booking->id,
                'sitter_name' => $booking->sitter->name,
                'service_title' => $booking->service->title,
                'start_date' => $booking->start_date->format('d.m.Y H:i')
            ],
            true
        );
    }

    public function notifyBookingCancelled(Booking $booking, User $cancelledBy): void
    {
        $recipient = $cancelledBy->id === $booking->owner_id ? $booking->sitter : $booking->owner;
        $cancellerName = $cancelledBy->name;

        $this->createNotification(
            $recipient,
            'booking_cancelled',
            'Rezerwacja anulowana',
            "Rezerwacja na \"{$booking->service->title}\" została anulowana przez {$cancellerName}.",
            [
                'booking_id' => $booking->id,
                'cancelled_by' => $cancellerName,
                'service_title' => $booking->service->title,
                'reason' => $booking->cancellation_reason
            ],
            true
        );
    }

    public function notifyBookingCompleted(Booking $booking): void
    {
        $this->createNotification(
            $booking->owner,
            'booking_completed',
            'Usługa zakończona!',
            "Usługa \"{$booking->service->title}\" została zakończona. Możesz teraz wystawić opinię.",
            [
                'booking_id' => $booking->id,
                'sitter_name' => $booking->sitter->name,
                'service_title' => $booking->service->title
            ]
        );
    }

    public function notifyPaymentCompleted(Payment $payment): void
    {
        $booking = $payment->booking;

        // Powiadom właściciela o zakończonej płatności
        $this->createNotification(
            $booking->owner,
            'payment_completed',
            'Płatność zakończona pomyślnie!',
            "Płatność za rezerwację \"{$booking->service->title}\" została zakończona pomyślnie.",
            [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'amount' => $payment->amount,
                'service_title' => $booking->service->title
            ]
        );

        // Powiadom opiekuna o nowej płatności
        $this->createNotification(
            $booking->sitter,
            'payment_completed',
            'Otrzymałeś nową płatność!',
            "Otrzymałeś płatność " . number_format($payment->sitter_amount, 2) . " zł za usługę \"{$booking->service->title}\".",
            [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'amount' => $payment->sitter_amount,
                'service_title' => $booking->service->title
            ],
            true
        );
    }

    public function notifyPaymentFailed(Payment $payment): void
    {
        $booking = $payment->booking;

        $this->createNotification(
            $booking->owner,
            'payment_failed',
            'Płatność nieudana',
            "Płatność za rezerwację \"{$booking->service->title}\" nie powiodła się. Spróbuj ponownie.",
            [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'service_title' => $booking->service->title
            ],
            true
        );
    }

    public function notifyBookingReminder(Booking $booking): void
    {
        // Przypomnienie dla opiekuna - 24h przed
        if ($booking->start_date->diffInHours(now()) <= 24) {
            $this->createNotification(
                $booking->sitter,
                'reminder',
                'Przypomnienie o rezerwacji',
                "Jutro masz zaplanowaną opiekę nad {$booking->pet->name} ({$booking->service->title}).",
                [
                    'booking_id' => $booking->id,
                    'pet_name' => $booking->pet->name,
                    'service_title' => $booking->service->title,
                    'start_date' => $booking->start_date->format('d.m.Y H:i')
                ]
            );
        }

        // Przypomnienie dla właściciela - 2h przed
        if ($booking->start_date->diffInHours(now()) <= 2) {
            $this->createNotification(
                $booking->owner,
                'reminder',
                'Przypomnienie o rezerwacji',
                "Za 2 godziny rozpoczyna się opieka nad {$booking->pet->name}.",
                [
                    'booking_id' => $booking->id,
                    'pet_name' => $booking->pet->name,
                    'sitter_name' => $booking->sitter->name,
                    'start_date' => $booking->start_date->format('d.m.Y H:i')
                ]
            );
        }
    }

    public function getUserNotifications(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
                   ->latest()
                   ->limit($limit)
                   ->get();
    }

    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
                   ->unread()
                   ->update(['read_at' => now()]);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    public function notifyReviewReceived(Review $review): void
    {
        $this->createNotification(
            $review->reviewee,
            'review_received',
            'Otrzymałeś nową recenzję!',
            "{$review->reviewer->name} wystawił Ci ocenę {$review->rating}/5 za usługę \"{$review->booking->service->title}\".",
            [
                'review_id' => $review->id,
                'booking_id' => $review->booking_id,
                'reviewer_name' => $review->reviewer->name,
                'rating' => $review->rating,
                'service_title' => $review->booking->service->title
            ]
        );
    }

    public function notifyMessageReceived(Message $message): void
    {
        $conversation = $message->conversation;
        $recipient = $conversation->getOtherUser($message->sender);

        $this->createNotification(
            $recipient,
            'message_received',
            'Nowa wiadomość!',
            "{$message->sender->name} wysłał Ci wiadomość.",
            [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender_name' => $message->sender->name,
                'booking_id' => $conversation->booking_id
            ]
        );
    }

    public function deleteOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($daysOld))
                          ->delete();
    }
}