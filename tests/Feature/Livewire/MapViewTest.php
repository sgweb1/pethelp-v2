<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MapView;
use Livewire\Livewire;
use Tests\TestCase;

class MapViewTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(MapView::class)
            ->assertStatus(200);
    }
}
