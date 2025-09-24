<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class NotificationCenter extends Component
{
    use WithPagination;

    public bool $showUnreadOnly = false;
    public string $filterType = '';

    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = Notification::where('id', $notificationId)
                                   ->where('user_id', Auth::id())
                                   ->first();

        if ($notification) {
            $this->notificationService->markAsRead($notification);
        }
    }

    public function markAllAsRead(): void
    {
        $this->notificationService->markAllAsRead(Auth::user());
        session()->flash('success', 'Wszystkie powiadomienia zostały oznaczone jako przeczytane.');
    }

    public function requestDeleteNotification(int $notificationId): void
    {
        $notification = Notification::where('id', $notificationId)
                                   ->where('user_id', Auth::id())
                                   ->first();

        if ($notification) {
            $this->dispatch('show-confirmation',
                'Usuń powiadomienie',
                'Czy na pewno chcesz usunąć to powiadomienie? Ta operacja jest nieodwracalna.',
                'delete-notification-confirmed',
                [$notificationId],
                'Usuń',
                'Anuluj'
            );
        }
    }

    #[On('delete-notification-confirmed')]
    public function deleteNotification(int $notificationId): void
    {
        $notification = Notification::where('id', $notificationId)
                                   ->where('user_id', Auth::id())
                                   ->first();

        if ($notification) {
            $notification->delete();
            $this->dispatch('show-toast', 'success', 'Powiadomienie zostało usunięte.');
        }
    }

    public function toggleUnreadFilter(): void
    {
        $this->showUnreadOnly = !$this->showUnreadOnly;
        $this->resetPage();
    }

    public function filterByType(string $type): void
    {
        $this->filterType = $type === $this->filterType ? '' : $type;
        $this->resetPage();
    }

    public function getNotificationsProperty()
    {
        $query = Auth::user()->notifications()->latest();

        if ($this->showUnreadOnly) {
            $query->unread();
        }

        if ($this->filterType) {
            $query->byType($this->filterType);
        }

        return $query->paginate(10);
    }

    public function getUnreadCountProperty(): int
    {
        return $this->notificationService->getUnreadCount(Auth::user());
    }

    public function getNotificationTypesProperty(): array
    {
        $types = Auth::user()->notifications()
                     ->selectRaw('type, COUNT(*) as count')
                     ->groupBy('type')
                     ->pluck('count', 'type')
                     ->toArray();

        $typeLabels = [
            'booking_created' => 'Nowe rezerwacje',
            'booking_confirmed' => 'Potwierdzone',
            'booking_cancelled' => 'Anulowane',
            'booking_completed' => 'Zakończone',
            'payment_completed' => 'Płatności',
            'payment_failed' => 'Błędy płatności',
            'reminder' => 'Przypomnienia'
        ];

        $result = [];
        foreach ($types as $type => $count) {
            $result[] = [
                'type' => $type,
                'label' => $typeLabels[$type] ?? ucfirst($type),
                'count' => $count
            ];
        }

        return $result;
    }

    public function render()
    {
        return view('livewire.notification-center');
    }
}