<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PetSitterWizard;
use Livewire\Livewire;
use Tests\TestCase;

class PetSitterWizardTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(PetSitterWizard::class)
            ->assertStatus(200);
    }
}
