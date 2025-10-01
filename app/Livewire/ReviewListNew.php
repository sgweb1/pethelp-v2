<?php

namespace App\Livewire;

use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewListNew extends Component
{
    use WithPagination;

    public ?User $user = null;

    public string $filterRating = '';

    public bool $showAll = false;

    public function mount(?User $user = null)
    {
        $this->user = $user;
    }

    public function filterByRating($rating)
    {
        $this->filterRating = $rating;
        $this->resetPage();
    }

    public function toggleShowAll()
    {
        $this->showAll = ! $this->showAll;
        $this->resetPage();
    }

    public function deleteReview($reviewId)
    {
        $review = Review::find($reviewId);

        if ($review && $review->canBeDeletedBy(Auth::user())) {
            $review->delete();
            session()->flash('success', 'Recenzja została usunięta.');
        } else {
            session()->flash('error', 'Nie możesz usunąć tej recenzji.');
        }
    }

    public function hideReview($reviewId)
    {
        $review = Review::find($reviewId);

        if ($review && ($review->reviewee_id === Auth::id() || Auth::user()->isAdmin())) {
            $review->hide();
            session()->flash('success', 'Recenzja została ukryta.');
        }
    }

    public function getReviewsProperty()
    {
        $query = Review::with(['reviewer', 'reviewee', 'booking.service'])
            ->visible();

        if ($this->user) {
            $query->forUser($this->user->id);
        }

        if ($this->filterRating) {
            $query->where('rating', $this->filterRating);
        }

        if (! $this->showAll) {
            $query->recent();
        }

        return $query->paginate(10);
    }

    public function getAverageRatingProperty(): float
    {
        $query = Review::visible();

        if ($this->user) {
            $query->forUser($this->user->id);
        }

        return $query->avg('rating') ?? 0;
    }

    public function getRatingDistributionProperty(): array
    {
        $query = Review::visible();

        if ($this->user) {
            $query->forUser($this->user->id);
        }

        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = (clone $query)->where('rating', $i)->count();
        }

        return $distribution;
    }

    public function render()
    {
        $breadcrumbs = [
            [
                'title' => 'Panel',
                'icon' => '🏠',
                'url' => route('profile.dashboard'),
            ],
            [
                'title' => 'Recenzje',
                'icon' => '⭐',
            ],
        ];

        return view('livewire.review-list')->layout('components.dashboard-layout', compact('breadcrumbs'));
    }
}
