# 🏆 Testing Best Practices - PetHelp

Przewodnik najlepszych praktyk testowania dla zespołu PetHelp.

## 🎯 Filozofia Testowania

### Piramida Testów
```
        🔺 E2E Tests
       🔺🔺 Integration Tests  
    🔺🔺🔺🔺 Unit Tests
```

**Nasze proporcje:**
- **70% Unit Tests** - szybkie, izolowane, niezawodne
- **20% Integration Tests** - API endpoints, komponenty
- **10% E2E Tests** - krytyczne ścieżki użytkownika

### Zasady AAA
Każdy test powinien mieć struktur AAA:
- **Arrange** - przygotowanie danych i stanu
- **Act** - wykonanie akcji/operacji  
- **Assert** - sprawdzenie rezultatu

## 🔧 Backend Best Practices (Laravel)

### 1. Nazewnictwo Testów

```php
// ✅ DOBRZE - opisowe nazwy
/** @test */
public function user_can_create_pet_with_valid_data()
{
    // Test implementation
}

/** @test */
public function guest_cannot_access_pet_endpoints()
{
    // Test implementation
}

// ❌ ŹLE - niejasne nazwy
/** @test */
public function test_pet_creation()
{
    // Test implementation
}
```

### 2. Użycie Factories zamiast ręcznego tworzenia

```php
// ✅ DOBRZE
$user = User::factory()->create();
$pet = Pet::factory()->dog()->healthy()->create(['owner_id' => $user->id]);

// ❌ ŹLE  
$user = new User([
    'name' => 'Test User',
    'email' => 'test@example.com',
    // ... wszystkie wymagane pola
]);
```

### 3. Testowanie Autoryzacji

```php
// ✅ DOBRZE - test dostępu
/** @test */
public function user_cannot_update_other_users_pet()
{
    $owner_id = User::factory()->create();
    $sitter_id = User::factory()->create();
    $pet = Pet::factory()->create(['owner_id' => $owner_id->id]);

    $response = $this->actingAs($sitter_id)
        ->putJson("/api/pets/{$pet->id}", ['name' => 'Hacked']);

    $response->assertStatus(404); // lub 403
}
```

### 4. Testowanie Walidacji

```php
// ✅ DOBRZE - test wszystkich wymaganych pól
/** @test */
public function create_pet_validates_required_fields()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/pets', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'species', 
            'age',
            'size'
        ]);
}
```

### 5. Izolacja Testów

```php
// ✅ DOBRZE - każdy test w izolacji
use RefreshDatabase;

/** @test */
public function each_test_has_clean_database()
{
    // Baza jest automatycznie wyczyszczona przed tym testem
    $this->assertDatabaseCount('pets', 0);
}
```

### 6. Testowanie Business Logic

```php
// ✅ DOBRZE - test reguł biznesowych  
/** @test */
public function cannot_delete_pet_with_active_bookings()
{
    $user = User::factory()->create();
    $pet = Pet::factory()->create(['owner_id' => $user->id]);
    
    // Utwórz aktywną rezerwację
    $pet->bookings()->create([
        'sitter_id' => User::factory()->create()->id,
        'status' => 'confirmed',
        'start_date' => now()->addDay(),
        'end_date' => now()->addDays(3),
        'price' => 100
    ]);

    $response = $this->actingAs($user)
        ->deleteJson("/api/pets/{$pet->id}");

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Cannot delete pet with active bookings'
        ]);
        
    $this->assertDatabaseHas('pets', ['id' => $pet->id]);
}
```

### 7. Testowanie File Uploads

```php
// ✅ DOBRZE - mock storage
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/** @test */
public function user_can_upload_pet_photo()
{
    Storage::fake('public');
    
    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('pet.jpg');

    $response = $this->actingAs($user)
        ->postJson('/api/pets', [
            'name' => 'Buddy',
            'species' => 'dog',
            'age' => 3,
            'size' => 'medium',
            'photo' => $photo
        ]);

    $response->assertStatus(201);
    
    $pet = Pet::where('name', 'Buddy')->first();
    Storage::disk('public')->assertExists($pet->photo);
}
```

## 🎨 Frontend Best Practices (Vue + Vitest)

### 1. Mocki Zależności

```typescript
// ✅ DOBRZE - mock przed importem
vi.mock('@inertiajs/vue3', () => ({
  usePage: vi.fn(),
  router: {
    visit: vi.fn()
  }
}))

import ComponentToTest from '@/Components/ComponentToTest.vue'
```

### 2. Testowanie Komponentów

```typescript
// ✅ DOBRZE - test zachowań, nie implementacji
it('displays error message when form is invalid', async () => {
  const wrapper = mount(PetForm)
  
  // Act
  await wrapper.find('form').trigger('submit')
  
  // Assert  
  expect(wrapper.find('.error-message').text())
    .toBe('Name is required')
})

// ❌ ŹLE - testowanie implementacji
it('calls validateForm method on submit', () => {
  const wrapper = mount(PetForm)
  const spy = vi.spyOn(wrapper.vm, 'validateForm')
  
  wrapper.find('form').trigger('submit')
  
  expect(spy).toHaveBeenCalled() // Testuje implementację!
})
```

### 3. Testowanie Composables

```typescript
// ✅ DOBRZE - test logic w izolacji
it('replaces placeholders in translations', async () => {
  const { usePage } = await import('@inertiajs/vue3')
  vi.mocked(usePage).mockReturnValue({
    props: {
      locale: 'en',
      translations: {
        'greeting': 'Hello :name!'
      }
    }
  })

  const { t } = useTranslations()
  
  expect(t('greeting', { name: 'John' }))
    .toBe('Hello John!')
})
```

### 4. Testowanie User Interactions

```typescript
// ✅ DOBRZE - testuj rzeczywiste interakcje
it('switches language when button is clicked', async () => {
  const { router } = await import('@inertiajs/vue3')
  const wrapper = mount(LanguageSwitcher)
  
  // Znajdź i kliknij przycisk
  const polishButton = wrapper.find('[data-testid="polish-btn"]')
  await polishButton.trigger('click')
  
  expect(router.visit).toHaveBeenCalledWith('/language/switch', {
    method: 'post',
    data: { locale: 'pl' }
  })
})
```

### 5. Async Testing

```typescript
// ✅ DOBRZE - async/await dla asynchronicznych operacji
it('loads pet data on mount', async () => {
  const mockPets = [{ id: 1, name: 'Buddy' }]
  window.axios.get.mockResolvedValue({ data: { pets: mockPets } })
  
  const wrapper = mount(PetList)
  
  // Poczekaj na async operations
  await nextTick()
  await flushPromises()
  
  expect(wrapper.find('[data-testid="pet-1"]').text())
    .toContain('Buddy')
})
```

## 🚀 Performance Best Practices

### 1. Backend Optimization

```php
// ✅ DOBRZE - użyj SQLite in-memory
// phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>

// ✅ DOBRZE - wyłącz niepotrzebne funkcje w testach
<env name="MAIL_MAILER" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="CACHE_DRIVER" value="array"/>
```

### 2. Frontend Optimization

```typescript
// ✅ DOBRZE - grupuj testy podobnej funkcjonalności
describe('LanguageSwitcher', () => {
  let wrapper: VueWrapper

  beforeEach(() => {
    wrapper = mount(LanguageSwitcher)
  })

  afterEach(() => {
    wrapper.unmount()
  })

  // Wszystkie testy używają tego samego setup
})
```

### 3. Selective Test Running

```bash
# ✅ DOBRZE - uruchamiaj tylko potrzebne testy podczas rozwoju

# Backend - konkretna klasa
php artisan test --filter=PetControllerTest

# Backend - konkretna metoda  
php artisan test --filter=test_user_can_create_pet

# Frontend - konkretny plik
npm run test -- LanguageSwitcher.test.ts

# Frontend - pattern matching
npm run test -- --testNamePattern="translation"
```

## 🐛 Common Pitfalls

### Backend Anti-patterns

```php
// ❌ ŹLE - testowanie implementacji
/** @test */
public function test_controller_calls_service_method()
{
    $service = Mockery::mock(PetService::class);
    $service->shouldReceive('createPet')->once();
    
    // Testuje implementację, nie zachowanie!
}

// ❌ ŹLE - zbyt ogólne asserty
$response->assertStatus(200); // Za mało konkretne

// ❌ ŹLE - testowanie wielu rzeczy naraz
/** @test */
public function test_pet_creation_and_update_and_deletion()
{
    // Za dużo w jednym teście!
}
```

### Frontend Anti-patterns

```typescript
// ❌ ŹLE - testowanie CSS
it('button has blue background', () => {
  const wrapper = mount(Component)
  expect(wrapper.find('button').classes()).toContain('bg-blue-500')
  // CSS powinno być testowane wizualnie!
})

// ❌ ŹLE - zbyt szczegółowe mocki
vi.mock('@/composables/useTranslations', () => ({
  useTranslations: () => ({
    t: (key) => key,
    locale: ref('en'),
    // ... zbyt dużo szczegółów
  })
}))
```

## 📊 Test Quality Metrics

### Code Coverage Goals
- **Unit Tests:** 85%+ coverage
- **Integration Tests:** 70%+ of critical paths  
- **E2E Tests:** 50%+ of user journeys

### Test Quality Checklist

**Backend Test Checklist:**
- [ ] Test ma opisową nazwę
- [ ] Używa Factory zamiast ręcznych danych
- [ ] Testuje jedno zachowanie
- [ ] Ma poprawne asserty
- [ ] Jest niezależny od innych testów
- [ ] Uruchamia się szybko (<100ms)

**Frontend Test Checklist:**
- [ ] Testuje zachowania, nie implementację
- [ ] Mocki są minimalne i celowe
- [ ] Używa async/await gdy potrzebne
- [ ] Testuje user interactions
- [ ] Ma czytelne asserty
- [ ] Jest izolowany

## 🔄 Test Maintenance

### Refactoring Tests

```php
// ✅ DOBRZE - wyodrębnij wspólne setup
abstract class PetTestCase extends TestCase
{
    protected User $user;
    protected Pet $pet;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->pet = Pet::factory()->create(['owner_id' => $this->user->id]);
    }
    
    protected function actingAsUser(): TestCase
    {
        return $this->actingAs($this->user);
    }
}
```

### Test Data Management

```php
// ✅ DOBRZE - używaj traits dla wspólnych danych
trait HasPetTestData
{
    protected function validPetData(): array
    {
        return [
            'name' => 'Test Pet',
            'species' => 'dog',
            'age' => 3,
            'size' => 'medium',
            'vaccinated' => true,
            'spayed_neutered' => true,
            'microchipped' => false,
        ];
    }
}
```

## 🎓 Continuous Learning

### Resources
- **Laravel Testing:** [Laravel Documentation](https://laravel.com/docs/testing)
- **Vue Testing:** [Vue Test Utils](https://test-utils.vuejs.org/)
- **Vitest Guide:** [Vitest Documentation](https://vitest.dev/)
- **Testing Philosophy:** [Testing Trophy](https://testingjavascript.com/)

### Team Standards
- **Code Review:** Wszystkie testy przechodzą przez review
- **Coverage Gates:** Minimum 80% coverage dla nowych funkcji
- **Performance:** Testy muszą być szybkie (<5s dla full suite)
- **Documentation:** Każdy test ma jasny cel i context

---

**Ostatnia aktualizacja:** 06.09.2025  
**Zespół:** PetHelp Development Team  
**Wersja:** v1.0