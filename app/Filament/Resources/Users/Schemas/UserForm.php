<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

/**
 * Schemat formularza dla zasobu User w panelu administracyjnym.
 *
 * Zawiera kompleksowy formularz podzielony na zakładki (Tabs) umożliwiający
 * zarządzanie wszystkimi danymi użytkownika i jego profilu.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 *
 * STRUCTURE:
 * - Tab "Podstawowe dane" - dane logowania i statusy premium
 * - Tab "Profil" - dane osobowe i identyfikacyjne
 * - Tab "Doświadczenie & Certyfikaty" - tylko dla sitters, doświadczenie zawodowe
 * - Tab "Lokalizacja & Dostępność" - dane geograficzne i harmonogram
 * - Tab "Dom & Środowisko" - informacje o miejscu zamieszkania
 * - Tab "Weryfikacja" - status weryfikacji i dokumenty
 * - Tab "Statystyki" - metryki tylko do odczytu
 */
class UserForm
{
    /**
     * Konfiguruje schemat formularza użytkownika.
     *
     * Tworzy pełny formularz z siedmioma zakładkami zawierającymi wszystkie
     * pola związane z użytkownikiem i jego profilem. Używa relacji profile.*
     * dla pól z modelu UserProfile.
     *
     * @param  Schema  $schema  Schemat Filament do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     *
     * @example
     * // Użycie w Resource
     * public static function form(Form $form): Form
     * {
     *     return $form->schema(
     *         UserForm::configure($form->getSchema())
     *     );
     * }
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User Details')
                    ->tabs([
                        // ======================================
                        // TAB 1: PODSTAWOWE DANE
                        // ======================================
                        Tabs\Tab::make('Podstawowe dane')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Pełne imię i nazwisko')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Nazwa wyświetlana publicznie'),

                                TextInput::make('email')
                                    ->label('Adres email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Używany do logowania i powiadomień'),

                                TextInput::make('password')
                                    ->label('Hasło')
                                    ->password()
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->helperText('Minimum 8 znaków. Zostaw puste jeśli nie chcesz zmieniać'),

                                DateTimePicker::make('email_verified_at')
                                    ->label('Data weryfikacji email')
                                    ->helperText('Ustaw aby oznaczyć email jako zweryfikowany'),

                                DateTimePicker::make('premium_until')
                                    ->label('Premium do')
                                    ->helperText('Data wygaśnięcia statusu premium')
                                    ->minDate(now()),
                            ]),

                        // ======================================
                        // TAB 2: PROFIL
                        // ======================================
                        Tabs\Tab::make('Profil')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Select::make('profile.role')
                                    ->label('Rola użytkownika')
                                    ->options([
                                        'owner' => 'Właściciel zwierzęcia',
                                        'sitter' => 'Opiekun',
                                        'both' => 'Właściciel i opiekun',
                                        'admin' => 'Administrator',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->helperText('Określa uprawnienia i dostępne funkcje'),

                                TextInput::make('profile.first_name')
                                    ->label('Imię')
                                    ->maxLength(255),

                                TextInput::make('profile.last_name')
                                    ->label('Nazwisko')
                                    ->maxLength(255),

                                TextInput::make('profile.phone')
                                    ->label('Telefon')
                                    ->tel()
                                    ->maxLength(20)
                                    ->helperText('Format: +48 123 456 789'),

                                RichEditor::make('profile.bio')
                                    ->label('O mnie')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'bulletList',
                                        'orderedList',
                                        'link',
                                    ])
                                    ->helperText('Opis widoczny publicznie na profilu'),

                                FileUpload::make('profile.avatar')
                                    ->label('Zdjęcie profilowe')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->helperText('Maksymalnie 2MB, format: JPG, PNG'),

                                Section::make('Adres')
                                    ->schema([
                                        Repeater::make('profile.address')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('street')
                                                    ->label('Ulica i numer')
                                                    ->required(),
                                                TextInput::make('city')
                                                    ->label('Miasto')
                                                    ->required(),
                                                TextInput::make('postal_code')
                                                    ->label('Kod pocztowy')
                                                    ->required()
                                                    ->mask('99-999'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(1)
                                            ->maxItems(1)
                                            ->addActionLabel('Dodaj adres'),
                                    ])
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ]),

                        // ======================================
                        // TAB 3: DOŚWIADCZENIE & CERTYFIKATY
                        // ======================================
                        Tabs\Tab::make('Doświadczenie & Certyfikaty')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                TextInput::make('profile.experience_years')
                                    ->label('Lata doświadczenia')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->suffix('lat')
                                    ->helperText('Liczba lat doświadczenia w opiece nad zwierzętami'),

                                Repeater::make('profile.certifications')
                                    ->label('Certyfikaty i uprawnienia')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nazwa certyfikatu')
                                            ->required(),
                                        TextInput::make('issuer')
                                            ->label('Wydawca'),
                                        TextInput::make('year')
                                            ->label('Rok uzyskania')
                                            ->numeric()
                                            ->minValue(1900)
                                            ->maxValue(date('Y')),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Dodaj certyfikat')
                                    ->collapsible()
                                    ->columnSpanFull(),

                                Toggle::make('profile.has_insurance')
                                    ->label('Posiada ubezpieczenie')
                                    ->helperText('Czy opiekun posiada ubezpieczenie OC')
                                    ->live(),

                                Textarea::make('profile.insurance_details')
                                    ->label('Szczegóły ubezpieczenia')
                                    ->rows(3)
                                    ->visible(fn ($get) => $get('profile.has_insurance'))
                                    ->helperText('Numer polisy, zakres ubezpieczenia, itp.')
                                    ->columnSpanFull(),

                                Repeater::make('profile.pets_experience')
                                    ->label('Doświadczenie ze zwierzętami')
                                    ->schema([
                                        Select::make('pet_type')
                                            ->label('Typ zwierzęcia')
                                            ->options([
                                                'dog' => 'Pies',
                                                'cat' => 'Kot',
                                                'bird' => 'Ptak',
                                                'fish' => 'Ryba',
                                                'rodent' => 'Gryzoń',
                                                'reptile' => 'Gad',
                                                'other' => 'Inne',
                                            ])
                                            ->required(),
                                        TextInput::make('years')
                                            ->label('Lata doświadczenia')
                                            ->numeric()
                                            ->suffix('lat'),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Dodaj doświadczenie')
                                    ->columnSpanFull(),
                            ]),

                        // ======================================
                        // TAB 4: LOKALIZACJA & DOSTĘPNOŚĆ
                        // ======================================
                        Tabs\Tab::make('Lokalizacja & Dostępność')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Koordynaty geograficzne')
                                    ->schema([
                                        TextInput::make('profile.latitude')
                                            ->label('Szerokość geograficzna')
                                            ->numeric()
                                            ->step(0.000001)
                                            ->helperText('Automatycznie wypełniane na podstawie adresu'),

                                        TextInput::make('profile.longitude')
                                            ->label('Długość geograficzna')
                                            ->numeric()
                                            ->step(0.000001)
                                            ->helperText('Automatycznie wypełniane na podstawie adresu'),
                                    ])
                                    ->columns(2)
                                    ->collapsed(),

                                TextInput::make('profile.service_radius')
                                    ->label('Promień obsługi')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->suffix('km')
                                    ->helperText('Maksymalny dystans świadczenia usług'),

                                Repeater::make('profile.weekly_availability')
                                    ->label('Dostępność w tygodniu')
                                    ->schema([
                                        Select::make('day')
                                            ->label('Dzień')
                                            ->options([
                                                'monday' => 'Poniedziałek',
                                                'tuesday' => 'Wtorek',
                                                'wednesday' => 'Środa',
                                                'thursday' => 'Czwartek',
                                                'friday' => 'Piątek',
                                                'saturday' => 'Sobota',
                                                'sunday' => 'Niedziela',
                                            ])
                                            ->required(),
                                        TextInput::make('from')
                                            ->label('Od')
                                            ->type('time')
                                            ->required(),
                                        TextInput::make('to')
                                            ->label('Do')
                                            ->type('time')
                                            ->required(),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Dodaj dostępność')
                                    ->columnSpanFull(),

                                Toggle::make('profile.emergency_available')
                                    ->label('Dostępny w nagłych przypadkach')
                                    ->helperText('Czy opiekun przyjmuje zlecenia w trybie awaryjnym'),
                            ]),

                        // ======================================
                        // TAB 5: DOM & ŚRODOWISKO
                        // ======================================
                        Tabs\Tab::make('Dom & Środowisko')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Select::make('profile.home_type')
                                    ->label('Typ mieszkania')
                                    ->options([
                                        'apartment' => 'Mieszkanie',
                                        'house' => 'Dom',
                                        'studio' => 'Kawalerka',
                                        'other' => 'Inne',
                                    ])
                                    ->native(false),

                                Toggle::make('profile.has_garden')
                                    ->label('Posiada ogród')
                                    ->helperText('Czy miejsce zamieszkania posiada ogród lub podwórko'),

                                Toggle::make('profile.is_smoking')
                                    ->label('Dom z palaczami')
                                    ->helperText('Czy w domu są osoby palące papierosy'),

                                Toggle::make('profile.has_other_pets')
                                    ->label('Posiada własne zwierzęta')
                                    ->live()
                                    ->helperText('Czy opiekun ma własne zwierzęta'),

                                Repeater::make('profile.other_pets')
                                    ->label('Własne zwierzęta')
                                    ->schema([
                                        Select::make('type')
                                            ->label('Typ')
                                            ->options([
                                                'dog' => 'Pies',
                                                'cat' => 'Kot',
                                                'bird' => 'Ptak',
                                                'fish' => 'Ryba',
                                                'rodent' => 'Gryzoń',
                                                'reptile' => 'Gad',
                                                'other' => 'Inne',
                                            ])
                                            ->required(),
                                        TextInput::make('breed')
                                            ->label('Rasa'),
                                        TextInput::make('age')
                                            ->label('Wiek')
                                            ->numeric()
                                            ->suffix('lat'),
                                    ])
                                    ->columns(3)
                                    ->visible(fn ($get) => $get('profile.has_other_pets'))
                                    ->addActionLabel('Dodaj zwierzę')
                                    ->columnSpanFull(),

                                FileUpload::make('profile.home_photos')
                                    ->label('Zdjęcia domu')
                                    ->image()
                                    ->multiple()
                                    ->maxFiles(10)
                                    ->maxSize(2048)
                                    ->imageEditor()
                                    ->reorderable()
                                    ->helperText('Maksymalnie 10 zdjęć, 2MB każde')
                                    ->columnSpanFull(),
                            ]),

                        // ======================================
                        // TAB 6: WERYFIKACJA
                        // ======================================
                        Tabs\Tab::make('Weryfikacja')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Toggle::make('profile.is_verified')
                                    ->label('Konto zweryfikowane')
                                    ->helperText('Czy użytkownik przeszedł proces weryfikacji'),

                                DateTimePicker::make('profile.verified_at')
                                    ->label('Data weryfikacji')
                                    ->helperText('Kiedy konto zostało zweryfikowane'),

                                Select::make('profile.verification_status')
                                    ->label('Status weryfikacji')
                                    ->options([
                                        'pending' => 'Oczekująca',
                                        'in_review' => 'W trakcie weryfikacji',
                                        'approved' => 'Zatwierdzona',
                                        'rejected' => 'Odrzucona',
                                    ])
                                    ->native(false)
                                    ->helperText('Aktualny status procesu weryfikacji'),

                                FileUpload::make('profile.verification_documents')
                                    ->label('Dokumenty weryfikacyjne')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->maxSize(5120)
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->helperText('Dowód osobisty, certyfikaty, itp. Maksymalnie 5 plików po 5MB')
                                    ->columnSpanFull(),
                            ]),

                        // ======================================
                        // TAB 7: STATYSTYKI
                        // ======================================
                        Tabs\Tab::make('Statystyki')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Placeholder::make('created_at')
                                    ->label('Data rejestracji')
                                    ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i') ?? 'Nie dotyczy'),

                                Placeholder::make('profile.total_bookings')
                                    ->label('Łączna liczba rezerwacji')
                                    ->content(fn ($record) => $record?->profile?->total_bookings ?? 0),

                                Placeholder::make('profile.rating_average')
                                    ->label('Średnia ocena')
                                    ->content(fn ($record) => $record?->profile?->rating_average
                                        ? number_format($record->profile->rating_average, 2).' ⭐'
                                        : 'Brak ocen'),

                                Placeholder::make('profile.reviews_count')
                                    ->label('Liczba opinii')
                                    ->content(fn ($record) => $record?->profile?->reviews_count ?? 0),

                                Placeholder::make('pets_count')
                                    ->label('Liczba zarejestrowanych zwierząt')
                                    ->content(fn ($record) => $record?->pets()->count() ?? 0),

                                Placeholder::make('services_count')
                                    ->label('Liczba oferowanych usług')
                                    ->content(fn ($record) => $record?->services()->count() ?? 0),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
