# Dokumentacja Komponentów UI - PetHelp

## Przegląd
Własne komponenty Blade w stylu Bootstrap z obsługą Alpine.js i Tailwind CSS.

---

## 1. Button (`<x-ui.button>`)

### Parametry:
- `variant` - styl przycisku (domyślnie: `primary`)
- `size` - rozmiar (domyślnie: `md`)
- `type` - typ HTML (domyślnie: `button`)
- `disabled` - czy wyłączony (domyślnie: `false`)
- `loading` - czy pokazać spinner (domyślnie: `false`)
- `icon` - ikona HTML
- `iconPosition` - pozycja ikony: `left`/`right` (domyślnie: `left`)

### Warianty:
`primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark`, `link`, `outline-primary`, `outline-secondary`

### Rozmiary:
`xs`, `sm`, `md`, `lg`, `xl`

### Przykłady użycia:

```blade
<!-- Podstawowy przycisk -->
<x-ui.button>Kliknij mnie</x-ui.button>

<!-- Przycisk z wariantem -->
<x-ui.button variant="success" size="lg">Zapisz</x-ui.button>

<!-- Przycisk z ikoną -->
<x-ui.button variant="primary" icon="🔍">Wyszukaj</x-ui.button>

<!-- Przycisk loading -->
<x-ui.button variant="primary" :loading="true">Ładowanie</x-ui.button>

<!-- Przycisk wyłączony -->
<x-ui.button variant="secondary" :disabled="true">Wyłączony</x-ui.button>

<!-- Link przycisk -->
<x-ui.button variant="link" onclick="window.location.href='/login'">Zaloguj się</x-ui.button>
```

---

## 2. Card (`<x-ui.card>`)

### Parametry:
- `header` - zawartość nagłówka
- `footer` - zawartość stopki
- `variant` - styl karty (domyślnie: `default`)
- `shadow` - czy dodać cień (domyślnie: `true`)
- `padding` - czy dodać padding (domyślnie: `true`)

### Warianty:
`default`, `primary`, `success`, `warning`, `danger`, `dark`

### Przykłady użycia:

```blade
<!-- Podstawowa karta -->
<x-ui.card>
    <p>Zawartość karty</p>
</x-ui.card>

<!-- Karta z nagłówkiem i stopką -->
<x-ui.card variant="primary">
    <x-slot:header>
        <h3 class="text-lg font-semibold">Tytuł karty</h3>
    </x-slot:header>

    <p>Główna zawartość karty</p>

    <x-slot:footer>
        <x-ui.button variant="primary">Akcja</x-ui.button>
    </x-slot:footer>
</x-ui.card>

<!-- Karta bez padding -->
<x-ui.card :padding="false">
    <img src="image.jpg" class="w-full">
    <div class="p-4">
        <p>Zawartość z obrazem</p>
    </div>
</x-ui.card>
```

---

## 3. Alert (`<x-ui.alert>`)

### Parametry:
- `type` - typ alertu (domyślnie: `info`)
- `dismissible` - czy można zamknąć (domyślnie: `false`)
- `icon` - czy pokazać ikonę (domyślnie: `true`)
- `title` - tytuł alertu

### Typy:
`primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark`

### Przykłady użycia:

```blade
<!-- Podstawowy alert -->
<x-ui.alert type="success">
    Operacja zakończona sukcesem!
</x-ui.alert>

<!-- Alert z tytułem -->
<x-ui.alert type="warning" title="Uwaga!">
    To jest ważna informacja.
</x-ui.alert>

<!-- Alert do zamknięcia -->
<x-ui.alert type="info" :dismissible="true">
    Ten alert można zamknąć.
</x-ui.alert>

<!-- Alert bez ikony -->
<x-ui.alert type="danger" :icon="false">
    Alert bez ikony.
</x-ui.alert>
```

---

## 4. Modal (`<x-ui.modal>`)

### Parametry:
- `id` - unikalny identyfikator (domyślnie: `modal`)
- `size` - rozmiar: `xs`, `sm`, `md`, `lg`, `xl`, `full` (domyślnie: `md`)
- `title` - tytuł modala
- `footer` - zawartość stopki
- `backdrop` - czy kliknięcie tła zamyka (domyślnie: `true`)
- `keyboard` - czy ESC zamyka (domyślnie: `true`)
- `static` - czy modal statyczny (domyślnie: `false`)

### Funkcje JavaScript:
- `openModal(id)` - otwiera modal
- `closeModal(id)` - zamyka modal

### Przykłady użycia:

```blade
<!-- Przycisk otwierający modal -->
<x-ui.button onclick="openModal('example-modal')">Otwórz Modal</x-ui.button>

<!-- Modal -->
<x-ui.modal id="example-modal" size="lg" title="Przykładowy Modal">
    <p>Zawartość modala</p>

    <x-slot:footer>
        <x-ui.button variant="secondary" onclick="closeModal('example-modal')">Anuluj</x-ui.button>
        <x-ui.button variant="primary">Zapisz</x-ui.button>
    </x-slot:footer>
</x-ui.modal>

<!-- Modal konfirmacji -->
<x-ui.modal id="confirm-modal" size="sm" title="Potwierdzenie" :static="true">
    <p>Czy na pewno chcesz usunąć ten element?</p>

    <x-slot:footer>
        <x-ui.button variant="danger">Usuń</x-ui.button>
        <x-ui.button variant="secondary" onclick="closeModal('confirm-modal')">Anuluj</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

---

## 5. Input (`<x-ui.input>`)

### Parametry:
- `type` - typ inputa (domyślnie: `text`)
- `label` - etykieta pola
- `error` - komunikat błędu
- `help` - tekst pomocniczy
- `required` - czy wymagane (domyślnie: `false`)
- `disabled` - czy wyłączone (domyślnie: `false`)
- `size` - rozmiar: `sm`, `md`, `lg` (domyślnie: `md`)
- `icon` - ikona HTML
- `iconPosition` - pozycja ikony: `left`/`right` (domyślnie: `left`)

### Przykłady użycia:

```blade
<!-- Podstawowy input -->
<x-ui.input
    name="email"
    type="email"
    label="Adres email"
    placeholder="Wprowadź email"
    :required="true"
/>

<!-- Input z błędem -->
<x-ui.input
    name="password"
    type="password"
    label="Hasło"
    error="Hasło musi mieć minimum 8 znaków"
    value="{{ old('password') }}"
/>

<!-- Input z ikoną -->
<x-ui.input
    name="search"
    label="Wyszukaj"
    icon="🔍"
    placeholder="Wpisz frazę..."
/>

<!-- Input z pomocą -->
<x-ui.input
    name="phone"
    label="Telefon"
    help="Format: +48 123 456 789"
    placeholder="+48"
/>

<!-- Duży input -->
<x-ui.input
    name="title"
    label="Tytuł"
    size="lg"
    placeholder="Wprowadź tytuł"
/>
```

---

## 6. Dropdown (`<x-ui.dropdown>`)

### Parametry:
- `trigger` - element wyzwalający dropdown
- `position` - pozycja: `top-left`, `top-right`, `bottom-left`, `bottom-right`, `left`, `right` (domyślnie: `bottom-left`)
- `width` - szerokość (domyślnie: `w-48`)

### Przykłady użycia:

```blade
<!-- Dropdown z przyciskiem -->
<x-ui.dropdown>
    <x-slot:trigger>
        <x-ui.button variant="secondary">
            Opcje
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </x-ui.button>
    </x-slot:trigger>

    <div class="py-1">
        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Opcja 1</a>
        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Opcja 2</a>
        <div class="border-t border-gray-100"></div>
        <a href="#" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50">Usuń</a>
    </div>
</x-ui.dropdown>

<!-- Dropdown użytkownika -->
<x-ui.dropdown position="bottom-right" width="w-56">
    <x-slot:trigger>
        <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
            <img class="h-8 w-8 rounded-full" src="avatar.jpg" alt="Avatar">
        </button>
    </x-slot:trigger>

    <div class="py-1">
        <div class="px-4 py-2 text-sm text-gray-700 border-b">
            <div class="font-medium">Jan Kowalski</div>
            <div class="text-gray-500">jan@example.com</div>
        </div>
        <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
        <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ustawienia</a>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Wyloguj</button>
        </form>
    </div>
</x-ui.dropdown>
```

---

## 7. Navbar (`<x-ui.navbar>`)

### Parametry:
- `brand` - element marki/logo
- `variant` - styl: `light`, `dark`, `primary`, `transparent` (domyślnie: `light`)
- `fixed` - czy przyklej do góry (domyślnie: `false`)
- `container` - czy użyć container (domyślnie: `true`)

### Sloty:
- Domyślny slot - menu desktop
- `mobileMenu` - oddzielne menu mobilne

### Przykłady użycia:

```blade
<!-- Podstawowa nawigacja -->
<x-ui.navbar variant="light" :fixed="true">
    <x-slot:brand>
        <a href="/" class="flex items-center">
            <span class="text-2xl">🐾</span>
            <span class="ml-2 text-xl font-bold">PetHelp</span>
        </a>
    </x-slot:brand>

    <a href="/" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md">Strona główna</a>
    <a href="/search" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md">Znajdź opiekuna</a>
    <a href="/become-sitter" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md">Zostań opiekunem</a>

    <div class="ml-auto flex items-center space-x-2">
        <x-ui.button variant="secondary" href="/login">Zaloguj</x-ui.button>
        <x-ui.button variant="primary" href="/register">Rejestracja</x-ui.button>
    </div>
</x-ui.navbar>

<!-- Nawigacja z dropdown -->
<x-ui.navbar variant="dark">
    <x-slot:brand>
        <span class="text-xl font-bold">PetHelp</span>
    </x-slot:brand>

    <a href="/dashboard" class="text-white hover:text-gray-300 px-3 py-2">Dashboard</a>

    <x-ui.dropdown>
        <x-slot:trigger>
            <button class="text-white hover:text-gray-300 px-3 py-2 flex items-center">
                Moje konto
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </x-slot:trigger>

        <a href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-100">Profil</a>
        <a href="/settings" class="block px-4 py-2 text-sm hover:bg-gray-100">Ustawienia</a>
    </x-ui.dropdown>
</x-ui.navbar>
```

---

## 8. Badge (`<x-ui.badge>`)

### Parametry:
- `variant` - styl: `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark` (domyślnie: `primary`)
- `size` - rozmiar: `xs`, `sm`, `md`, `lg` (domyślnie: `md`)
- `pill` - czy okrągły (domyślnie: `false`)
- `removable` - czy można usunąć (domyślnie: `false`)

### Przykłady użycia:

```blade
<!-- Podstawowy badge -->
<x-ui.badge>Nowy</x-ui.badge>

<!-- Badge z wariantem -->
<x-ui.badge variant="success">Aktywny</x-ui.badge>

<!-- Badge pill -->
<x-ui.badge variant="warning" :pill="true">Oczekuje</x-ui.badge>

<!-- Badge do usunięcia -->
<x-ui.badge variant="info" :removable="true">Tag</x-ui.badge>

<!-- Mały badge -->
<x-ui.badge variant="danger" size="sm">3</x-ui.badge>

<!-- Badge w tekście -->
<h2>
    Wiadomości
    <x-ui.badge variant="primary" size="sm">5</x-ui.badge>
</h2>
```

---

## 9. Accordion (`<x-ui.accordion>`)

### Parametry:
- `items` - tablica elementów `[['title' => '', 'content' => ''], ...]`
- `multiple` - czy wiele może być otwartych (domyślnie: `false`)
- `flush` - czy bez obramowania (domyślnie: `false`)

### Przykłady użycia:

```blade
@php
$faqItems = [
    [
        'title' => 'Jak znaleźć opiekuna?',
        'content' => 'Użyj naszej wyszukiwarki, wprowadź lokalizację i wybierz odpowiedniego opiekuna dla swojego pupila.'
    ],
    [
        'title' => 'Ile kosztuje opieka?',
        'content' => 'Ceny są ustalane przez opiekunów i zależą od typu usługi. Sprawdź profile opiekunów, aby porównać ceny.'
    ],
    [
        'title' => 'Czy opiekunowie są ubezpieczeni?',
        'content' => 'Tak, wszyscy weryfikowani opiekunowie mają ubezpieczenie odpowiedzialności cywilnej.'
    ]
];
@endphp

<!-- Accordion FAQ -->
<x-ui.accordion :items="$faqItems" />

<!-- Accordion z wieloma otwartymi -->
<x-ui.accordion :items="$faqItems" :multiple="true" />

<!-- Accordion flush -->
<x-ui.accordion :items="$faqItems" :flush="true" />
```

---

## 10. Toast (`<x-ui.toast>`)

### Parametry:
- `type` - typ: `success`, `error`, `warning`, `info` (domyślnie: `info`)
- `title` - tytuł powiadomienia
- `timeout` - czas wyświetlania w ms (domyślnie: `5000`)
- `position` - pozycja: `top-left`, `top-right`, `top-center`, `bottom-left`, `bottom-right`, `bottom-center` (domyślnie: `top-right`)
- `dismissible` - czy można zamknąć (domyślnie: `true`)

### Funkcja JavaScript:
`showToast(message, type, timeout)`

### Przykłady użycia:

```blade
<!-- Toast w Blade -->
<x-ui.toast type="success" title="Sukces">
    Dane zostały zapisane pomyślnie!
</x-ui.toast>

<!-- Toast przez JavaScript -->
<script>
// Sukces
showToast('Operacja zakończona sukcesem!', 'success');

// Błąd
showToast('Wystąpił błąd podczas zapisywania.', 'error');

// Ostrzeżenie
showToast('Sprawdź wprowadzone dane.', 'warning', 10000);

// Info
showToast('Nowa wersja aplikacji jest dostępna.', 'info');
</script>

<!-- Toast w Livewire -->
<script>
document.addEventListener('livewire:load', function () {
    Livewire.on('showToast', data => {
        showToast(data.message, data.type);
    });
});
</script>
```

---

## JavaScript API

### Funkcje globalne:
- `showToast(message, type, timeout)` - pokazuje powiadomienie
- `openModal(id)` - otwiera modal
- `closeModal(id)` - zamyka modal
- `FormValidator` - obiekt z funkcjami walidacji
- `ComponentAnimations` - predefiniowane animacje

### Przykład walidacji formularza:

```javascript
// Walidacja w Alpine.js
<div x-data="{
    email: '',
    password: '',
    errors: {},

    validateForm() {
        this.errors = {};

        if (!FormValidator.validateRequired(this.email)) {
            this.errors.email = 'Email jest wymagany';
        } else if (!FormValidator.validateEmail(this.email)) {
            this.errors.email = 'Nieprawidłowy format email';
        }

        if (!FormValidator.validateMinLength(this.password, 8)) {
            this.errors.password = 'Hasło musi mieć minimum 8 znaków';
        }

        return Object.keys(this.errors).length === 0;
    },

    submitForm() {
        if (this.validateForm()) {
            showToast('Formularz wysłany!', 'success');
        }
    }
}">
    <x-ui.input
        x-model="email"
        type="email"
        label="Email"
        :error="errors.email"
    />

    <x-ui.input
        x-model="password"
        type="password"
        label="Hasło"
        :error="errors.password"
    />

    <x-ui.button @click="submitForm()">Wyślij</x-ui.button>
</div>
```

---

## Style CSS

Komponenty używają klas Tailwind CSS. Dodatkowe style dla toastów:

```css
/* W resources/css/app.css */
.toast-success { @apply bg-green-50 border-green-200 text-green-800; }
.toast-error { @apply bg-red-50 border-red-200 text-red-800; }
.toast-warning { @apply bg-yellow-50 border-yellow-200 text-yellow-800; }
.toast-info { @apply bg-blue-50 border-blue-200 text-blue-800; }
```

---

## Konfiguracja w app.js

```javascript
// resources/js/app.js
import './bootstrap';
import './components';

// Globalne ustawienia
window.PetHelpUI = {
    defaultToastTimeout: 5000,
    defaultModalSize: 'md',
    animations: true
};
```

Ta dokumentacja pomoże Ci efektywnie wykorzystywać wszystkie komponenty UI w projekcie PetHelp!