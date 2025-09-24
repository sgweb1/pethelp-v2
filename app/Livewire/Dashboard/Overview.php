<?php

namespace App\Livewire\Dashboard;

use App\Models\Pet;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Advertisement;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Overview extends Component
{
    #[Computed]
    public function quickStats()
    {
        $user = auth()->user();

        return [
            'pets_count' => $user->pets()->count(),
            'active_services' => $user->isSitter() ? $user->services()->where('is_active', true)->count() : 0,
            'upcoming_bookings' => $user->isSitter()
                ? $user->sitterBookings()->where('start_date', '>', now())->count()
                : $user->ownerBookings()->where('start_date', '>', now())->count(),
            'rating' => $user->isSitter() ? $user->reviewsReceived()->avg('rating') : null,
        ];
    }

    #[Computed]
    public function upcomingEvents()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $events = collect();

        // Nadchodzące zlecenia
        if ($user->isSitter()) {
            $bookings = $user->sitterBookings()
                ->where('start_date', '>=', $today)
                ->where('status', '!=', 'cancelled')
                ->with(['user', 'service'])
                ->orderBy('start_date')
                ->limit(3)
                ->get();

            foreach ($bookings as $booking) {
                $events->push([
                    'type' => 'booking',
                    'title' => 'Zlecenie: ' . $booking->service->title,
                    'subtitle' => 'Klient: ' . $booking->user->name,
                    'date' => $booking->start_date,
                    'time' => $booking->start_date->format('H:i'),
                    'icon' => '💼',
                    'color' => 'blue'
                ]);
            }
        }

        // Nadchodzące wydarzenia
        $userEvents = $user->events()
            ->where('starts_at', '>=', $today)
            ->orderBy('starts_at')
            ->limit(2)
            ->get();

        foreach ($userEvents as $event) {
            $events->push([
                'type' => 'event',
                'title' => $event->title,
                'subtitle' => 'Wydarzenie',
                'date' => $event->starts_at,
                'time' => $event->starts_at->format('H:i'),
                'icon' => '🎉',
                'color' => 'green'
            ]);
        }

        // Przypomnienia o zwierzętach (próbne dane)
        if ($user->pets()->count() > 0) {
            $events->push([
                'type' => 'reminder',
                'title' => 'Szczepienie',
                'subtitle' => $user->pets()->first()->name ?? 'Pupil',
                'date' => Carbon::tomorrow(),
                'time' => '10:00',
                'icon' => '💉',
                'color' => 'yellow'
            ]);
        }

        return $events->sortBy('date')->take(5);
    }

    #[Computed]
    public function recentActivity()
    {
        $user = auth()->user();
        $activities = collect();

        // Ostatnie recenzje
        if ($user->isSitter()) {
            $recentReviews = $user->reviewsReceived()
                ->with('user')
                ->latest()
                ->limit(2)
                ->get();

            foreach ($recentReviews as $review) {
                $activities->push([
                    'type' => 'review',
                    'title' => 'Nowa recenzja od ' . $review->user->name,
                    'description' => '⭐ ' . $review->rating . '/5 - ' . Str::limit($review->comment, 50),
                    'time' => $review->created_at,
                    'icon' => '⭐',
                    'color' => 'yellow'
                ]);
            }
        }

        // Ostatnie wiadomości (próbne)
        $activities->push([
            'type' => 'message',
            'title' => 'Nowa wiadomość',
            'description' => 'Pytanie o dostępność w weekend',
            'time' => Carbon::now()->subHours(2),
            'icon' => '💬',
            'color' => 'blue'
        ]);

        // Ostatnie płatności
        if ($user->isSitter()) {
            $activities->push([
                'type' => 'payment',
                'title' => 'Płatność otrzymana',
                'description' => 'Za opiekę nad Luna - 120 zł',
                'time' => Carbon::now()->subDay(),
                'icon' => '💰',
                'color' => 'green'
            ]);
        }

        return $activities->sortByDesc('time')->take(4);
    }

    #[Computed]
    public function myPetsPreview()
    {
        return auth()->user()->pets()
            ->with('petType')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.overview');
    }
}
