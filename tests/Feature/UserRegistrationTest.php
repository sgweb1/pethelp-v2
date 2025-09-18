<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_displays_with_default_owner_role(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Właściciel');
        $response->assertSee('Opiekun');
    }

    public function test_registration_page_displays_with_sitter_role_selected(): void
    {
        $response = $this->get('/register?role=sitter');

        $response->assertStatus(200);
        $response->assertSee('border-indigo-500 bg-indigo-50');
    }

    public function test_user_can_register_as_owner(): void
    {
        $response = $this->post('/register', [
            'name' => 'jankowalski',
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'owner',
            'phone' => '+48123456789',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'name' => 'jankowalski',
            'email' => 'jan@example.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'role' => 'owner',
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'phone' => '+48123456789',
        ]);

        $user = User::where('email', 'jan@example.com')->first();
        $this->assertTrue($user->isOwner());
        $this->assertFalse($user->isSitter());
    }

    public function test_user_can_register_as_sitter(): void
    {
        $response = $this->post('/register', [
            'name' => 'annanowak',
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'email' => 'anna@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'sitter',
            'phone' => '+48987654321',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'name' => 'annanowak',
            'email' => 'anna@example.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'role' => 'sitter',
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'phone' => '+48987654321',
        ]);

        $user = User::where('email', 'anna@example.com')->first();
        $this->assertTrue($user->isSitter());
        $this->assertFalse($user->isOwner());
    }

    public function test_registration_requires_all_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors([
            'name', 'first_name', 'last_name', 'email', 'password', 'role'
        ]);
    }

    public function test_registration_validates_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_phone_is_optional(): void
    {
        $response = $this->post('/register', [
            'name' => 'testuser',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'owner',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('user_profiles', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => null,
        ]);
    }
}
