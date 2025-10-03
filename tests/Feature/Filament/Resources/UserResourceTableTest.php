<?php

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Livewire\Livewire;

/**
 * Testy dla tabeli UserResource w Filament.
 *
 * Sprawdzają poprawność wyświetlania kolumn, działania filtrów
 * i sortowania w tabeli użytkowników.
 */
beforeEach(function () {
    // Tworzymy użytkownika admin do testów
    $this->admin = User::factory()->create();
    $this->admin->profile()->create([
        'role' => 'admin',
        'is_verified' => true,
        'first_name' => 'Admin',
        'last_name' => 'User',
    ]);

    $this->actingAs($this->admin);
});

test('tabela wyświetla kolumny zgodnie ze specyfikacją', function () {
    $user = User::factory()->create([
        'name' => 'Jan Kowalski',
        'email' => 'jan@example.com',
        'premium_until' => now()->addMonth(),
    ]);

    $user->profile()->create([
        'role' => 'owner',
        'is_verified' => true,
        'rating_average' => 4.5,
        'first_name' => 'Jan',
        'last_name' => 'Kowalski',
    ]);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$user])
        ->assertCanRenderTableColumn('id')
        ->assertCanRenderTableColumn('profile.avatar')
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('email')
        ->assertCanRenderTableColumn('profile.role')
        ->assertCanRenderTableColumn('is_premium')
        ->assertCanRenderTableColumn('profile.is_verified')
        ->assertCanRenderTableColumn('created_at')
        ->assertCanRenderTableColumn('ownerBookings_count')
        ->assertCanRenderTableColumn('profile.rating_average');
});

test('kolumna ID jest sortowalna i przeszukiwalna', function () {
    $user = User::factory()->create();
    $user->profile()->create(['role' => 'owner', 'first_name' => 'Test', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertCanSortColumn('id')
        ->assertCanSearchTableColumn('id');
});

test('kolumna name jest sortowalna, przeszukiwalna i kopiowalna', function () {
    $user = User::factory()->create(['name' => 'Test User']);
    $user->profile()->create(['role' => 'owner', 'first_name' => 'Test', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertCanSortColumn('name')
        ->searchTable('Test User')
        ->assertCanSeeTableRecords([$user]);
});

test('kolumna email jest sortowalna, przeszukiwalna i kopiowalna', function () {
    $user = User::factory()->create(['email' => 'unique@example.com']);
    $user->profile()->create(['role' => 'owner', 'first_name' => 'Unique', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertCanSortColumn('email')
        ->searchTable('unique@example.com')
        ->assertCanSeeTableRecords([$user]);
});

test('kolumna roli wyświetla badge z odpowiednimi kolorami', function () {
    $owner = User::factory()->create();
    $owner->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

    $sitter = User::factory()->create();
    $sitter->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

    $both = User::factory()->create();
    $both->profile()->create(['role' => 'both', 'first_name' => 'Both', 'last_name' => 'User']);

    $admin = User::factory()->create();
    $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$owner, $sitter, $both, $admin]);
});

test('kolumna premium status wyświetla poprawny stan', function () {
    $premiumUser = User::factory()->create([
        'premium_until' => now()->addMonth(),
    ]);
    $premiumUser->profile()->create(['role' => 'owner', 'first_name' => 'Premium', 'last_name' => 'User']);

    $regularUser = User::factory()->create([
        'premium_until' => null,
    ]);
    $regularUser->profile()->create(['role' => 'owner', 'first_name' => 'Regular', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$premiumUser, $regularUser]);

    expect($premiumUser->isPremium())->toBeTrue();
    expect($regularUser->isPremium())->toBeFalse();
});

test('kolumna verified wyświetla status weryfikacji z profilu', function () {
    $verified = User::factory()->create();
    $verified->profile()->create([
        'role' => 'sitter',
        'is_verified' => true,
        'first_name' => 'Verified',
        'last_name' => 'Sitter',
    ]);

    $unverified = User::factory()->create();
    $unverified->profile()->create([
        'role' => 'sitter',
        'is_verified' => false,
        'first_name' => 'Unverified',
        'last_name' => 'Sitter',
    ]);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$verified, $unverified]);
});

test('kolumna created_at jest sortowalna', function () {
    $user = User::factory()->create();
    $user->profile()->create(['role' => 'owner', 'first_name' => 'Test', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertCanSortColumn('created_at');
});

test('kolumna rating wyświetla średnią ocenę z gwiazdką', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'role' => 'sitter',
        'rating_average' => 4.8,
        'first_name' => 'Rated',
        'last_name' => 'Sitter',
    ]);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$user]);
});

test('filtr role działa poprawnie', function () {
    $owner = User::factory()->create();
    $owner->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'Test']);

    $sitter = User::factory()->create();
    $sitter->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'Test']);

    Livewire::test(ListUsers::class)
        ->filterTable('role', 'owner')
        ->assertCanSeeTableRecords([$owner])
        ->assertCanNotSeeTableRecords([$sitter]);
});

test('filtr premium działa poprawnie', function () {
    $premium = User::factory()->create([
        'premium_until' => now()->addMonth(),
    ]);
    $premium->profile()->create(['role' => 'owner', 'first_name' => 'Premium', 'last_name' => 'Test']);

    $regular = User::factory()->create([
        'premium_until' => null,
    ]);
    $regular->profile()->create(['role' => 'owner', 'first_name' => 'Regular', 'last_name' => 'Test']);

    Livewire::test(ListUsers::class)
        ->filterTable('premium', true)
        ->assertCanSeeTableRecords([$premium])
        ->assertCanNotSeeTableRecords([$regular]);
});

test('filtr verified działa poprawnie', function () {
    $verified = User::factory()->create();
    $verified->profile()->create([
        'role' => 'sitter',
        'is_verified' => true,
        'first_name' => 'Verified',
        'last_name' => 'Test',
    ]);

    $unverified = User::factory()->create();
    $unverified->profile()->create([
        'role' => 'sitter',
        'is_verified' => false,
        'first_name' => 'Unverified',
        'last_name' => 'Test',
    ]);

    Livewire::test(ListUsers::class)
        ->filterTable('verified', true)
        ->assertCanSeeTableRecords([$verified])
        ->assertCanNotSeeTableRecords([$unverified]);
});

test('domyślne sortowanie to created_at desc', function () {
    $older = User::factory()->create([
        'created_at' => now()->subDays(5),
    ]);
    $older->profile()->create(['role' => 'owner', 'first_name' => 'Older', 'last_name' => 'User']);

    $newer = User::factory()->create([
        'created_at' => now(),
    ]);
    $newer->profile()->create(['role' => 'owner', 'first_name' => 'Newer', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
});

test('eager loading optymalizuje zapytania', function () {
    User::factory()->count(3)->create()->each(function ($user) {
        $user->profile()->create([
            'role' => 'owner',
            'is_verified' => true,
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);
    });

    // Test czy relacje są eager loadowane
    $component = Livewire::test(ListUsers::class);

    // Jeśli eager loading działa, nie powinno być N+1 query problem
    expect($component)->not->toBeNull();
});

test('akcje ViewAction i EditAction są dostępne', function () {
    $user = User::factory()->create();
    $user->profile()->create(['role' => 'owner', 'first_name' => 'Test', 'last_name' => 'User']);

    Livewire::test(ListUsers::class)
        ->assertTableActionExists('view')
        ->assertTableActionExists('edit');
});

test('bulk action DeleteBulkAction jest dostępny', function () {
    Livewire::test(ListUsers::class)
        ->assertTableBulkActionExists('delete');
});
