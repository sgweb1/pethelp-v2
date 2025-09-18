<?php

namespace Tests\Feature\Livewire\Pets;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_render(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/pets/create');

        $response->assertStatus(200);
    }
}
