<?php

use App\Models\User;
use App\Policies\UserPolicy;

/**
 * Testy dla UserPolicy - autoryzacja operacji na użytkownikach.
 *
 * Sprawdzają poprawność uprawnień dla różnych ról użytkowników:
 * - Administratorzy: pełne uprawnienia
 * - Zwykli użytkownicy: ograniczone uprawnienia do własnego profilu
 */
beforeEach(function () {
    $this->policy = new UserPolicy;
});

describe('viewAny - przeglądanie listy użytkowników', function () {
    test('administrator może przeglądać listę użytkowników', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        expect($this->policy->viewAny($admin))->toBeTrue();
    });

    test('zwykły użytkownik nie może przeglądać listy użytkowników', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Regular', 'last_name' => 'User']);

        expect($this->policy->viewAny($user))->toBeFalse();
    });

    test('opiekun nie może przeglądać listy użytkowników', function () {
        $sitter = User::factory()->create();
        $sitter->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->viewAny($sitter))->toBeFalse();
    });
});

describe('view - przeglądanie konkretnego użytkownika', function () {
    test('administrator może przeglądać profil każdego użytkownika', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'owner', 'first_name' => 'Other', 'last_name' => 'User']);

        expect($this->policy->view($admin, $otherUser))->toBeTrue();
    });

    test('użytkownik może przeglądać własny profil', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Regular', 'last_name' => 'User']);

        expect($this->policy->view($user, $user))->toBeTrue();
    });

    test('użytkownik nie może przeglądać profilu innego użytkownika', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->view($user, $otherUser))->toBeFalse();
    });
});

describe('create - tworzenie nowych użytkowników', function () {
    test('administrator może tworzyć użytkowników', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        expect($this->policy->create($admin))->toBeTrue();
    });

    test('zwykły użytkownik nie może tworzyć użytkowników', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->create($user))->toBeFalse();
    });

    test('opiekun nie może tworzyć użytkowników', function () {
        $sitter = User::factory()->create();
        $sitter->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->create($sitter))->toBeFalse();
    });
});

describe('update - edytowanie użytkowników', function () {
    test('administrator może edytować każdego użytkownika', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->update($admin, $otherUser))->toBeTrue();
    });

    test('użytkownik może edytować własny profil', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->update($user, $user))->toBeTrue();
    });

    test('użytkownik nie może edytować profilu innego użytkownika', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->update($user, $otherUser))->toBeFalse();
    });

    test('administrator może edytować własny profil', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        expect($this->policy->update($admin, $admin))->toBeTrue();
    });
});

describe('delete - usuwanie użytkowników', function () {
    test('administrator może usuwać użytkowników', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->delete($admin, $otherUser))->toBeTrue();
    });

    test('administrator nie może usunąć samego siebie', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        expect($this->policy->delete($admin, $admin))->toBeFalse();
    });

    test('zwykły użytkownik nie może usuwać użytkowników', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->delete($user, $otherUser))->toBeFalse();
    });

    test('użytkownik nie może usunąć samego siebie', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->delete($user, $user))->toBeFalse();
    });
});

describe('restore - przywracanie usuniętych użytkowników', function () {
    test('administrator może przywracać użytkowników', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $deletedUser = User::factory()->create();
        $deletedUser->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->restore($admin, $deletedUser))->toBeTrue();
    });

    test('zwykły użytkownik nie może przywracać użytkowników', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $deletedUser = User::factory()->create();
        $deletedUser->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->restore($user, $deletedUser))->toBeFalse();
    });
});

describe('forceDelete - permanentne usuwanie użytkowników', function () {
    test('administrator może permanentnie usuwać użytkowników', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->forceDelete($admin, $otherUser))->toBeTrue();
    });

    test('zwykły użytkownik nie może permanentnie usuwać użytkowników', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->forceDelete($user, $otherUser))->toBeFalse();
    });
});

describe('grantPremium - przyznawanie statusu premium', function () {
    test('administrator może przyznawać premium', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->grantPremium($admin, $user))->toBeTrue();
    });

    test('zwykły użytkownik nie może przyznawać premium', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->grantPremium($user, $otherUser))->toBeFalse();
    });

    test('administrator może przyznać premium samemu sobie', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        expect($this->policy->grantPremium($admin, $admin))->toBeTrue();
    });
});

describe('verifyAccount - weryfikacja kont użytkowników', function () {
    test('administrator może weryfikować konta', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $user = User::factory()->create();
        $user->profile()->create(['role' => 'sitter', 'is_verified' => false, 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->verifyAccount($admin, $user))->toBeTrue();
    });

    test('zwykły użytkownik nie może weryfikować kont', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'sitter', 'is_verified' => false, 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->verifyAccount($user, $otherUser))->toBeFalse();
    });

    test('użytkownik nie może weryfikować własnego konta', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'sitter', 'is_verified' => false, 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->verifyAccount($user, $user))->toBeFalse();
    });
});

describe('banUser - banowanie użytkowników', function () {
    test('administrator może banować użytkowników', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        expect($this->policy->banUser($admin, $user))->toBeTrue();
    });

    test('administrator nie może zbanować samego siebie', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        expect($this->policy->banUser($admin, $admin))->toBeFalse();
    });

    test('zwykły użytkownik nie może banować użytkowników', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        $otherUser = User::factory()->create();
        $otherUser->profile()->create(['role' => 'sitter', 'first_name' => 'Sitter', 'last_name' => 'User']);

        expect($this->policy->banUser($user, $otherUser))->toBeFalse();
    });
});

describe('integracja z Filament - dostęp do zasobów', function () {
    test('tylko administratorzy mogą uzyskać dostęp do UserResource', function () {
        $admin = User::factory()->create();
        $admin->profile()->create(['role' => 'admin', 'first_name' => 'Admin', 'last_name' => 'User']);

        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        // Admin może przeglądać listę
        expect($admin->can('viewAny', User::class))->toBeTrue();

        // Zwykły użytkownik nie może
        expect($user->can('viewAny', User::class))->toBeFalse();
    });

    test('użytkownicy mogą przeglądać i edytować własne profile przez can() helper', function () {
        $user = User::factory()->create();
        $user->profile()->create(['role' => 'owner', 'first_name' => 'Owner', 'last_name' => 'User']);

        // Użytkownik może przeglądać własny profil
        expect($user->can('view', $user))->toBeTrue();

        // Użytkownik może edytować własny profil
        expect($user->can('update', $user))->toBeTrue();
    });
});
