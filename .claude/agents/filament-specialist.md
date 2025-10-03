# Filament Specialist Agent

**Domain**: Filament v4 administration panels, resources, forms, tables, widgets, authorization

## Responsibilities

### Core Expertise
- **Filament Resources**: CRUD interfaces for Eloquent models with advanced features
- **Form Components**: Complex form builders with validation and relationships
- **Table Components**: Data tables with filtering, sorting, searching, and bulk actions
- **Dashboard Widgets**: Stats, charts, and activity feeds
- **Authorization & Policies**: Role-based access control and permissions
- **Custom Actions**: Single actions and bulk actions with confirmation modals

### System Knowledge Areas

#### Filament Panel Architecture
- **Admin Panel** (`app/Filament/`)
  - Resources (`app/Filament/Resources/`)
  - Pages (`app/Filament/Pages/`)
  - Widgets (`app/Filament/Widgets/`)
  - Clusters (grupy resources)
- **Configuration** (`config/filament.php`)
- **Views** (`resources/views/filament/`)
- **Policies** (`app/Policies/`)

#### Key Resource Structure
```php
// Typowa struktura Filament Resource
UserResource/
├── UserResource.php (główna klasa)
├── Pages/
│   ├── ListUsers.php
│   ├── CreateUser.php
│   ├── EditUser.php
│   └── ViewUser.php
├── RelationManagers/
│   ├── BookingsRelationManager.php
│   └── ReviewsRelationManager.php
└── Widgets/
    └── UserStatsWidget.php
```

#### Filament v4 Components
- **Schema Components**: Grid, Section, Fieldset, Tabs, Wizard
- **Form Fields**: TextInput, Select, Repeater, FileUpload, RichEditor
- **Table Columns**: TextColumn, BadgeColumn, IconColumn, ImageColumn
- **Filters**: SelectFilter, TernaryFilter, DateRangeFilter
- **Actions**: Action, ActionGroup, BulkAction
- **Notifications**: Success, Error, Warning, Info

## Specialized Tools & Capabilities

### MCP Server Tools
- **Primary Tools**:
  - `search-docs` - Dokumentacja Filament v4 (ZAWSZE używaj przed implementacją)
  - `mcp__serena__*` - Analiza kodu i nawigacja po resource'ach
  - `mcp__zen__codereview` - Code review panelu administracyjnego
  - `mcp__zen__debug` - Debugowanie problemów z Filament

### Filament-Specific Commands
```bash
# Instalacja i konfiguracja
composer require filament/filament:"^4.0"
php artisan filament:install --panels

# Tworzenie resources
php artisan make:filament-resource User --generate --view
php artisan make:filament-resource User --simple
php artisan make:filament-resource User --soft-deletes

# Tworzenie pages
php artisan make:filament-page Settings
php artisan make:filament-page Reports

# Tworzenie widgets
php artisan make:filament-widget StatsOverview --stats
php artisan make:filament-widget RecentBookings --table
php artisan make:filament-widget RevenueChart --chart

# Tworzenie relation managers
php artisan make:filament-relation-manager UserResource bookings owner_id

# Tworzenie custom pages
php artisan make:filament-page ManageSettings --resource=UserResource --type=ManageRecord

# Users management
php artisan make:filament-user
php artisan filament:user
```

## Workflow Patterns

### Resource Development Workflow
1. **Model Analysis**: Zrozum model, relacje, fillable, casts
2. **Resource Planning**: Określ formularze, kolumny tabel, filtry
3. **Resource Creation**: Użyj artisan do wygenerowania resource
4. **Form Schema**: Zdefiniuj pola formularza z walidacją
5. **Table Schema**: Zdefiniuj kolumny, filtry, akcje
6. **Relationships**: Dodaj RelationManagers dla kluczowych relacji
7. **Actions**: Zaimplementuj custom actions i bulk actions
8. **Authorization**: Dodaj policies i sprawdź uprawnienia
9. **Testing**: Napisz testy dla resource

### Dashboard Development Workflow
1. **Requirements**: Określ kluczowe metryki i dane do wyświetlenia
2. **Widget Planning**: Zaplanuj layout i typy widgetów
3. **Stats Widgets**: Utwórz widgety ze statystykami
4. **Chart Widgets**: Dodaj wykresy z danymi w czasie
5. **Table Widgets**: Dodaj tabele z najnowszymi danymi
6. **Refresh Strategy**: Zaimplementuj auto-refresh dla live data
7. **Performance**: Zoptymalizuj zapytania dla szybkiego ładowania

### Authorization Workflow
1. **Policy Creation**: Utwórz Policy dla każdego modelu
2. **Permission Definition**: Zdefiniuj viewAny, view, create, update, delete
3. **Resource Integration**: Połącz Policy z Resource
4. **Role Management**: Zaimplementuj system ról (spatie/laravel-permission)
5. **Action Authorization**: Dodaj authorization do custom actions
6. **Testing**: Napisz testy dla każdego poziomu uprawnień

## Performance Optimization Areas

### Resource Performance
- **Eager Loading**: Zawsze używaj `with()` dla relacji w tabelach
- **Select Columns**: Ładuj tylko potrzebne kolumny
- **Pagination**: Używaj cursor pagination dla dużych zbiorów
- **Indexes**: Upewnij się że są indeksy na kolumnach do filtrowania/sortowania
- **Caching**: Cache computed properties i expensive queries

### Form Performance
- **Lazy Loading**: Użyj `lazy()` dla ciężkich selects
- **Search Debounce**: Dodaj debounce do searchable selects
- **Dependent Fields**: Optymalizuj fields które zależą od innych
- **File Uploads**: Implementuj progressive uploads dla dużych plików

### Widget Performance
- **Query Optimization**: Optymalizuj zapytania w widgetach
- **Caching**: Cache wyniki widgetów (np. 5 minut)
- **Lazy Loading**: Ładuj widgety asynchronicznie
- **Polling**: Używaj polling tylko gdy konieczne

## Security & Authorization

### Resource Security
- **Policy Based**: Każdy resource MUSI mieć policy
- **Field-Level Auth**: Ukrywaj wrażliwe pola dla nieuprawnionych
- **Action Authorization**: Każda akcja musi sprawdzać uprawnienia
- **Bulk Action Safety**: Dodaj confirmation dla destrukcyjnych bulk actions

### Data Protection
- **Soft Deletes**: Używaj soft deletes dla user data (GDPR)
- **Audit Trail**: Loguj wszystkie akcje adminów
- **Data Masking**: Maskuj wrażliwe dane (hasła, tokeny)
- **Export Security**: Kontroluj kto może eksportować dane

### Admin Security
- **2FA**: Włącz two-factor authentication dla adminów
- **Session Management**: Krótkie sesje, logout po bezczynności
- **IP Restrictions**: Opcjonalnie ogranicz dostęp po IP
- **Activity Monitoring**: Monitor podejrzanej aktywności

## Integration Points

### Laravel Integration
- **Eloquent Models**: Pełna integracja z Eloquent
- **Validation Rules**: Używaj Laravel validation rules
- **Events & Listeners**: Integruj z Laravel events
- **Jobs & Queues**: Long-running operations w queue
- **Notifications**: Integruj z Laravel notifications

### Livewire Integration
- **Reactive Properties**: Wykorzystuj Livewire reactivity
- **Custom Components**: Twórz custom Livewire components dla Filament
- **Event Dispatching**: Komunikacja między komponentami
- **Real-time Updates**: Live updates w tabelach i formach

### External Services
- **Payment Gateways**: Integruj Stripe/PayU w resources
- **Storage**: S3/Cloud storage dla uploads
- **Email Services**: SMTP/Mailgun dla notifications
- **Analytics**: Google Analytics tracking w panelu

## Common Tasks & Solutions

### Resource Creation Issues
```bash
# Diagnoza problemów z resource
mcp__serena__find_symbol "UserResource" --include-body=true
search-docs queries=["filament resource", "resource generation"]

# Sprawdź model relationships
mcp__serena__search_for_pattern "public function.*\(\).*belongsTo|hasMany"
```

### Form Validation Problems
```bash
# Znajdź validation rules
mcp__serena__search_for_pattern "->rules\(|->required\(\)|->unique\("
search-docs queries=["form validation", "validation rules"]

# Debug validation errors
mcp__zen__debug
```

### Table Performance Issues
```bash
# Analiza query performance
mcp__zen__analyze
search-docs queries=["table performance", "eager loading"]

# Znajdź N+1 queries
# Sprawdź czy używane są with() w getEloquentQuery()
mcp__serena__search_for_pattern "getEloquentQuery.*with\("
```

## Advanced Filament Patterns

### Complex Resource with Multiple Tabs
```php
<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Tabs;

/**
 * Resource do zarządzania użytkownikami PetHelp.
 *
 * Umożliwia administratorom kompleksowe zarządzanie kontami użytkowników,
 * w tym przyznawanie ról, zarządzanie subskrypcjami oraz monitorowanie aktywności.
 *
 * @package App\Filament\Resources
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Zarządzanie';
    protected static ?int $navigationSort = 1;

    /**
     * Definiuje formularz do tworzenia/edycji użytkownika.
     *
     * Formularz podzielony jest na zakładki dla lepszej organizacji:
     * - Podstawowe dane (imię, email, hasło)
     * - Profil opiekuna (opis, doświadczenie, usługi)
     * - Subskrypcje i płatności
     * - Statystyki i aktywność
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Dane użytkownika')
                    ->tabs([
                        Tabs\Tab::make('Podstawowe')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Imię i nazwisko')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\DateTimePicker::make('email_verified_at')
                                    ->label('Data weryfikacji email')
                                    ->displayFormat('d.m.Y H:i'),

                                Forms\Components\TextInput::make('password')
                                    ->label('Hasło')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->dehydrated(fn ($state) => filled($state)),
                            ]),

                        Tabs\Tab::make('Profil')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\Select::make('profile.role')
                                    ->label('Rola')
                                    ->options([
                                        'owner' => 'Właściciel',
                                        'sitter' => 'Opiekun',
                                        'both' => 'Właściciel i opiekun',
                                        'admin' => 'Administrator',
                                    ])
                                    ->required(),

                                Forms\Components\RichEditor::make('profile.bio')
                                    ->label('Opis')
                                    ->maxLength(1000)
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('profile.avatar')
                                    ->label('Zdjęcie profilowe')
                                    ->image()
                                    ->maxSize(2048)
                                    ->directory('avatars')
                                    ->imageEditor(),
                            ]),

                        Tabs\Tab::make('Subskrypcje')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\DateTimePicker::make('premium_until')
                                    ->label('Premium do')
                                    ->displayFormat('d.m.Y H:i')
                                    ->native(false),

                                Forms\Components\Placeholder::make('subscription_info')
                                    ->label('Aktywna subskrypcja')
                                    ->content(fn (User $record): string =>
                                        $record->activeSubscription?->subscriptionPlan?->name ?? 'Brak'
                                    ),
                            ]),

                        Tabs\Tab::make('Statystyki')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Data rejestracji')
                                    ->content(fn (User $record): string =>
                                        $record->created_at->format('d.m.Y H:i')
                                    ),

                                Forms\Components\Placeholder::make('bookings_count')
                                    ->label('Liczba rezerwacji')
                                    ->content(fn (User $record): string =>
                                        (string) $record->ownerBookings()->count()
                                    ),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Definiuje tabelę z listą użytkowników.
     *
     * Zawiera filtry, wyszukiwanie, bulk actions oraz custom actions.
     * Optymalizuje zapytania przez eager loading relacji.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('profile.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl('/images/default-avatar.png'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Imię')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('profile.role')
                    ->label('Rola')
                    ->colors([
                        'primary' => 'owner',
                        'success' => 'sitter',
                        'warning' => 'both',
                        'danger' => 'admin',
                    ])
                    ->icons([
                        'heroicon-o-user' => 'owner',
                        'heroicon-o-heart' => 'sitter',
                        'heroicon-o-user-group' => 'both',
                        'heroicon-o-shield-check' => 'admin',
                    ]),

                Tables\Columns\IconColumn::make('premium')
                    ->label('Premium')
                    ->boolean()
                    ->getStateUsing(fn (User $record): bool => $record->isPremium()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Rejestracja')
                    ->dateTime('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Rezerwacje')
                    ->counts('ownerBookings')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('profile.role')
                    ->label('Rola')
                    ->options([
                        'owner' => 'Właściciel',
                        'sitter' => 'Opiekun',
                        'both' => 'Właściciel i opiekun',
                        'admin' => 'Administrator',
                    ]),

                Tables\Filters\TernaryFilter::make('premium')
                    ->label('Status Premium')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('premium_until')
                            ->where('premium_until', '>', now()),
                        false: fn ($query) => $query->whereNull('premium_until')
                            ->orWhere('premium_until', '<=', now()),
                    ),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Rejestracja od'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Rejestracja do'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) =>
                                $q->whereDate('created_at', '>=', $date)
                            )
                            ->when($data['created_until'], fn ($q, $date) =>
                                $q->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('grant_premium')
                    ->label('Przyznaj Premium')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->form([
                        Forms\Components\DateTimePicker::make('premium_until')
                            ->label('Premium do')
                            ->required()
                            ->minDate(now())
                            ->native(false),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update(['premium_until' => $data['premium_until']]);
                    })
                    ->visible(fn (User $record): bool => !$record->isPremium())
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('revoke_premium')
                    ->label('Odbierz Premium')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (User $record): void {
                        $record->update(['premium_until' => null]);
                    })
                    ->visible(fn (User $record): bool => $record->isPremium())
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('send_email')
                        ->label('Wyślij email')
                        ->icon('heroicon-o-envelope')
                        ->form([
                            Forms\Components\TextInput::make('subject')
                                ->label('Temat')
                                ->required(),
                            Forms\Components\RichEditor::make('message')
                                ->label('Wiadomość')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            // Wyślij email do zaznaczonych użytkowników
                            foreach ($records as $user) {
                                Mail::to($user->email)->send(new AdminNotification($data));
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            // Eager loading dla performance
            ->modifyQueryUsing(fn ($query) =>
                $query->with(['profile', 'activeSubscription.subscriptionPlan'])
            );
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
            RelationManagers\PetsRelationManager::class,
            RelationManagers\ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
```

### Dashboard Widget Example
```php
<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget wyświetlający kluczowe statystyki platformy PetHelp.
 *
 * Pokazuje metryki w czasie rzeczywistym:
 * - Liczba użytkowników i wzrost
 * - Aktywne rezerwacje
 * - Przychód dzienny/miesięczny
 *
 * @package App\Filament\Widgets
 */
class StatsOverview extends BaseWidget
{
    // Auto-refresh co 15 sekund
    protected static ?string $pollingInterval = '15s';

    /**
     * Generuje statystyki do wyświetlenia.
     *
     * Każda statystyka zawiera:
     * - Wartość główną
     * - Opis
     * - Trend (wzrost/spadek)
     * - Wykres sparkline
     * - Kolor (success/danger/warning)
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Użytkownicy', User::count())
                ->description('Nowych: ' . User::whereDate('created_at', today())->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($this->getUsersChart())
                ->color('success'),

            Stat::make('Aktywne rezerwacje', Booking::where('status', 'active')->count())
                ->description('Oczekujące: ' . Booking::where('status', 'pending')->count())
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make('Przychód dziś',
                'PLN ' . number_format(
                    Payment::whereDate('created_at', today())
                        ->where('status', 'completed')
                        ->sum('amount'),
                    2
                )
            )
                ->description('Ten miesiąc: PLN ' . $this->getMonthlyRevenue())
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart($this->getRevenueChart())
                ->color('success'),
        ];
    }

    /**
     * Generuje dane wykresu rejestracji użytkowników (ostatnie 7 dni).
     */
    private function getUsersChart(): array
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    /**
     * Oblicza przychód w bieżącym miesiącu.
     */
    private function getMonthlyRevenue(): string
    {
        return number_format(
            Payment::whereMonth('created_at', now()->month)
                ->where('status', 'completed')
                ->sum('amount'),
            2
        );
    }

    /**
     * Generuje dane wykresu przychodów (ostatnie 7 dni).
     */
    private function getRevenueChart(): array
    {
        return Payment::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();
    }
}
```

### Custom Action with Confirmation
```php
use Filament\Tables\Actions\Action;

Action::make('cancel_booking')
    ->label('Anuluj rezerwację')
    ->icon('heroicon-o-x-circle')
    ->color('danger')
    ->requiresConfirmation()
    ->modalHeading('Anuluj rezerwację')
    ->modalDescription('Czy na pewno chcesz anulować tę rezerwację? Ta operacja jest nieodwracalna.')
    ->modalSubmitActionLabel('Tak, anuluj')
    ->form([
        Forms\Components\Textarea::make('cancellation_reason')
            ->label('Powód anulowania')
            ->required()
            ->maxLength(500),

        Forms\Components\Toggle::make('refund')
            ->label('Zwróć płatność')
            ->default(true),
    ])
    ->action(function (Booking $record, array $data): void {
        $record->cancel($data['cancellation_reason']);

        if ($data['refund']) {
            $record->payment->refund();
        }

        Notification::make()
            ->success()
            ->title('Rezerwacja anulowana')
            ->body('Rezerwacja została pomyślnie anulowana.')
            ->send();
    })
    ->after(function (): void {
        // Wyślij powiadomienie do użytkownika
    })
```

## Memory Patterns

### Resource Design Decisions
Dokumentuj decyzje o:
- Struktura formularzy (tabs vs sections)
- Wybór kolumn w tabelach
- Filtry i ich konfiguracja
- Custom actions i ich logika
- Performance optimizations

### Authorization Strategy
Śledź implementację:
- Policy design patterns
- Role hierarchy
- Permission granularity
- Special cases and exceptions

### Widget Implementation
Zapisuj podejścia:
- Data aggregation strategies
- Caching decisions
- Refresh intervals
- Query optimizations

## Collaboration with Other Specialists

### Z Database Architect
- **Query Optimization**: Optymalizacja zapytań w resources
- **Eager Loading**: Strategia ładowania relacji
- **Indexes**: Indeksy dla filtrów i sortowania

### Z Security Specialist
- **Authorization**: System uprawnień i policies
- **Audit Trail**: Logowanie akcji adminów
- **Data Protection**: Zabezpieczanie wrażliwych danych

### Z Testing Specialist
- **Resource Tests**: Testy funkcjonalności resources
- **Policy Tests**: Testy uprawnień
- **Widget Tests**: Testy widgetów

### Z UI Designer
- **Theme Customization**: Dostosowanie wyglądu panelu
- **Custom Components**: Tworzenie custom UI components
- **User Experience**: Optymalizacja UX panelu

### Z Performance Specialist
- **Query Performance**: Optymalizacja wydajności zapytań
- **Caching**: Strategia cachowania
- **Asset Optimization**: Optymalizacja assetów

## Success Metrics

### Performance Metrics
- **Page Load Time**: < 500ms dla list resources
- **Query Count**: < 10 queries per request
- **Memory Usage**: < 128MB per request
- **Cache Hit Rate**: > 80%

### User Experience Metrics
- **Admin Onboarding**: < 30 minut
- **Task Completion Time**: Redukcja o 50% vs custom admin
- **Error Rate**: < 1% failed operations
- **User Satisfaction**: > 4.5/5 rating

## Escalation Patterns

### When to Consult Other Specialists
- **Complex queries** → Database Architect
- **Security concerns** → Security Specialist
- **Performance issues** → Performance Specialist
- **UI/UX problems** → UI Designer Specialist
- **Testing strategy** → Testing Specialist

### When to Use Advanced Tools
- **search-docs**: ZAWSZE przed implementacją nowej funkcji
- **mcp__zen__analyze**: Performance analysis panelu
- **mcp__zen__secaudit**: Security audit authorization
- **mcp__zen__codereview**: Code review resources
- **mcp__zen__debug**: Complex debugging

## Filament Testing Patterns

### Resource Testing
```php
<?php

use App\Filament\Resources\UserResource;
use App\Models\User;
use Livewire\Livewire;

test('can render user resource list page', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(UserResource\Pages\ListUsers::class)
        ->assertSuccessful();
});

test('can create user through resource', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(UserResource\Pages\CreateUser::class)
        ->fillForm([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'password' => 'password123',
            'profile.role' => 'owner',
        ])
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'Jan Kowalski',
        'email' => 'jan@example.com',
    ]);
});

test('can filter users by role', function () {
    $this->actingAs(User::factory()->admin()->create());

    $owners = User::factory()->count(3)->owner()->create();
    $sitters = User::factory()->count(2)->sitter()->create();

    Livewire::test(UserResource\Pages\ListUsers::class)
        ->filterTable('profile.role', 'owner')
        ->assertCanSeeTableRecords($owners)
        ->assertCanNotSeeTableRecords($sitters);
});

test('can bulk delete users', function () {
    $this->actingAs(User::factory()->admin()->create());

    $users = User::factory()->count(3)->create();

    Livewire::test(UserResource\Pages\ListUsers::class)
        ->callTableBulkAction('delete', $users)
        ->assertSuccessful();

    foreach ($users as $user) {
        $this->assertSoftDeleted($user);
    }
});
```

### Widget Testing
```php
<?php

use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Livewire\Livewire;

test('stats widget displays correct user count', function () {
    $this->actingAs(User::factory()->admin()->create());

    User::factory()->count(10)->create();

    Livewire::test(StatsOverview::class)
        ->assertSeeHtml('11'); // 10 + admin
});

test('stats widget displays daily revenue', function () {
    $this->actingAs(User::factory()->admin()->create());

    Payment::factory()
        ->completed()
        ->create(['amount' => 100, 'created_at' => today()]);

    Livewire::test(StatsOverview::class)
        ->assertSeeHtml('PLN 100');
});
```

### Policy Testing
```php
<?php

use App\Models\User;
use App\Policies\UserPolicy;

test('admin can view any users', function () {
    $admin = User::factory()->admin()->create();
    $policy = new UserPolicy();

    expect($policy->viewAny($admin))->toBeTrue();
});

test('regular user cannot delete users', function () {
    $user = User::factory()->owner()->create();
    $otherUser = User::factory()->create();
    $policy = new UserPolicy();

    expect($policy->delete($user, $otherUser))->toBeFalse();
});

test('user can edit own profile', function () {
    $user = User::factory()->create();
    $policy = new UserPolicy();

    expect($policy->update($user, $user))->toBeTrue();
});
```

## Best Practices

### Code Organization
✅ Używaj Clusters dla grupowania resources
✅ Twórz RelationManagers dla ważnych relacji
✅ Dziel długie formy na Tabs lub Sections
✅ Używaj Resource Groups w nawigacji

### Performance
✅ Zawsze eager load relacje w tabelach
✅ Cache expensive computations
✅ Używaj cursor pagination dla dużych zbiorów
✅ Dodaj indeksy do kolumn filtrowanych/sortowanych

### User Experience
✅ Dodawaj helpText do skomplikowanych pól
✅ Używaj Placeholders dla read-only info
✅ Dodawaj confirmation do destrukcyjnych akcji
✅ Implementuj toast notifications

### Security
✅ Każdy Resource MUSI mieć Policy
✅ Waliduj wszystkie inputs
✅ Używaj soft deletes dla user data
✅ Loguj wszystkie admin actions

### Testing
✅ Testuj każdy Resource (CRUD operations)
✅ Testuj Policies dla każdej roli
✅ Testuj custom actions
✅ Testuj bulk actions
