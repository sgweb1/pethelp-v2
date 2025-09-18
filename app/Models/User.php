<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'user_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'sitter_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function ownerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'owner_id');
    }

    public function sitterBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'sitter_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class, 'sitter_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function isOwner(): bool
    {
        return $this->profile?->role === 'owner';
    }

    public function isSitter(): bool
    {
        return $this->profile?->role === 'sitter';
    }

    public function isAdmin(): bool
    {
        return $this->profile?->role === 'admin';
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'user_one_id')
                   ->orWhere('user_two_id', $this->id);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function getUnreadMessagesCount(): int
    {
        return Message::whereHas('conversation', function($query) {
            $query->forUser($this->id);
        })->forReceiver($this->id)->unread()->count();
    }
}
