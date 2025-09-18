<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Location;
use App\Models\Service;
use App\Models\Pet;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Availability;
use App\Models\Notification;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_attributes(): void
    {
        $user = new User();

        $expected = ['name', 'email', 'password'];

        $this->assertEquals($expected, $user->getFillable());
    }

    public function test_user_has_hidden_attributes(): void
    {
        $user = new User();

        $expected = ['password', 'remember_token'];

        $this->assertEquals($expected, $user->getHidden());
    }

    public function test_user_casts_email_verified_at_to_datetime(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => '2024-01-15 10:30:00'
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->make();
        $user->password = 'plain-password';
        $user->save();

        $this->assertNotEquals('plain-password', $user->password);
        $this->assertTrue(\Hash::check('plain-password', $user->password));
    }

    public function test_user_has_profile_relationship(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::create([
            'user_id' => $user->id,
            'role' => 'sitter',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertInstanceOf(UserProfile::class, $user->profile);
        $this->assertEquals($profile->id, $user->profile->id);
    }

    public function test_user_has_locations_relationship(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->locations);
        $this->assertCount(1, $user->locations);
        $this->assertEquals($location->id, $user->locations->first()->id);
    }

    public function test_user_has_services_relationship(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->withSitter($user->id)->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->services);
        $this->assertCount(1, $user->services);
        $this->assertEquals($service->id, $user->services->first()->id);
    }

    public function test_user_has_pets_relationship(): void
    {
        $user = User::factory()->create();

        // Test that the relationship exists and returns empty collection by default
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->pets);
        $this->assertCount(0, $user->pets);
    }

    public function test_user_has_owner_bookings_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->ownerBookings);
        $this->assertCount(0, $user->ownerBookings);
    }

    public function test_user_has_sitter_bookings_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->sitterBookings);
        $this->assertCount(0, $user->sitterBookings);
    }

    public function test_user_can_be_created_with_factory(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    public function test_user_is_owner_method(): void
    {
        $user = User::factory()->create();

        // No profile = false
        $this->assertFalse($user->isOwner());

        // Create owner profile
        UserProfile::create([
            'user_id' => $user->id,
            'role' => 'owner',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $user->refresh();
        $this->assertTrue($user->isOwner());
    }

    public function test_user_is_sitter_method(): void
    {
        $user = User::factory()->create();

        // No profile = false
        $this->assertFalse($user->isSitter());

        // Create sitter profile
        UserProfile::create([
            'user_id' => $user->id,
            'role' => 'sitter',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $user->refresh();
        $this->assertTrue($user->isSitter());
    }

    public function test_user_is_admin_method(): void
    {
        $user = User::factory()->create();

        // No profile = false
        $this->assertFalse($user->isAdmin());

        // Create admin profile
        UserProfile::create([
            'user_id' => $user->id,
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
        ]);

        $user->refresh();
        $this->assertTrue($user->isAdmin());
    }

    public function test_user_has_reviews_given_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->reviewsGiven);
        $this->assertCount(0, $user->reviewsGiven);
    }

    public function test_user_has_reviews_received_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->reviewsReceived);
        $this->assertCount(0, $user->reviewsReceived);
    }

    public function test_user_has_availability_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->availability);
        $this->assertCount(0, $user->availability);
    }

    public function test_user_has_notifications_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->notifications);
        $this->assertCount(0, $user->notifications);
    }

    public function test_user_has_conversations_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->conversations);
        $this->assertCount(0, $user->conversations);
    }

    public function test_user_has_sent_messages_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->sentMessages);
        $this->assertCount(0, $user->sentMessages);
    }

    public function test_user_can_have_multiple_locations(): void
    {
        $user = User::factory()->create();

        Location::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->fresh()->locations);
    }

    public function test_user_can_have_multiple_services(): void
    {
        $user = User::factory()->create();

        Service::factory()->count(2)->withSitter($user->id)->create();

        $this->assertCount(2, $user->fresh()->services);
    }

    public function test_user_role_methods_work_with_different_roles(): void
    {
        $user = User::factory()->create();

        // Test sitter role
        UserProfile::create([
            'user_id' => $user->id,
            'role' => 'sitter',
            'first_name' => 'Test',
            'last_name' => 'Sitter',
        ]);

        $user->refresh();
        $this->assertTrue($user->isSitter());
        $this->assertFalse($user->isOwner());
        $this->assertFalse($user->isAdmin());

        // Update to owner role
        $user->profile->update(['role' => 'owner']);
        $user->refresh();

        $this->assertFalse($user->isSitter());
        $this->assertTrue($user->isOwner());
        $this->assertFalse($user->isAdmin());

        // Update to admin role
        $user->profile->update(['role' => 'admin']);
        $user->refresh();

        $this->assertFalse($user->isSitter());
        $this->assertFalse($user->isOwner());
        $this->assertTrue($user->isAdmin());
    }
}