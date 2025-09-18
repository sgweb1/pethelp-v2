<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class ReviewForm extends Component
{
    public Booking $booking;
    public ?Review $existingReview = null;

    #[Validate('required|integer|min:1|max:5')]
    public $rating = '';

    #[Validate('nullable|string|max:1000')]
    public $comment = '';

    public function mount(Booking $booking)
    {
        $this->booking = $booking;

        // Check if user already has a review for this booking
        $this->existingReview = $this->booking->getReviewBy(Auth::user());

        if ($this->existingReview) {
            $this->rating = $this->existingReview->rating;
            $this->comment = $this->existingReview->comment;
        }
    }

    public function submit()
    {
        // Check if user can review this booking
        if (!$this->booking->canBeReviewedBy(Auth::user()) && !$this->existingReview) {
            session()->flash('error', 'Nie możesz napisać recenzji dla tej rezerwacji.');
            return;
        }

        $this->validate();

        // Determine who is being reviewed
        $revieweeId = Auth::id() === $this->booking->owner_id
            ? $this->booking->sitter_id
            : $this->booking->owner_id;

        if ($this->existingReview && $this->existingReview->canBeEditedBy(Auth::user())) {
            // Update existing review
            $this->existingReview->update([
                'rating' => $this->rating,
                'comment' => $this->comment,
            ]);

            session()->flash('success', 'Recenzja została zaktualizowana!');
        } else {
            // Create new review
            $review = Review::create([
                'booking_id' => $this->booking->id,
                'reviewer_id' => Auth::id(),
                'reviewee_id' => $revieweeId,
                'rating' => $this->rating,
                'comment' => $this->comment,
            ]);

            // Send notification
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyReviewReceived($review);

            session()->flash('success', 'Recenzja została dodana!');
        }

        $this->dispatch('reviewSubmitted');
    }

    public function getRevieweeProperty()
    {
        return Auth::id() === $this->booking->owner_id
            ? $this->booking->sitter
            : $this->booking->owner;
    }

    public function getCanEditProperty(): bool
    {
        return $this->existingReview && $this->existingReview->canBeEditedBy(Auth::user());
    }

    public function render()
    {
        return view('livewire.review-form');
    }
}
