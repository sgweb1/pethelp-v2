# PetHelp Design System

## 🎨 Przegląd

PetHelp Design System to kompleksowy zestaw komponentów UI, tokenów designu i guidelines dla aplikacji PetHelp - polskiej platformy opieki nad zwierzętami.

## 🎯 Kluczowe Zasady

- **Spójność**: Jednolity wygląd i zachowanie w całej aplikacji
- **Dostępność**: WCAG 2.1 AA compliance
- **Responsywność**: Mobile-first approach
- **Performance**: Zoptymalizowane komponenty
- **Polskie UX**: Dostosowane do polskich użytkowników

## 🎨 Design Tokens

### Kolory Brand

```css
--color-primary-50: #eff6ff;
--color-primary-600: #2563eb;
--color-primary-700: #1d4ed8;
```

### Kolory Pet-friendly

```css
--color-warm-500: #d97706;    /* Pomarańczowy */
--color-nature-500: #10b981;  /* Zielony */
```

### Semantic Colors

```css
--color-success-600: #059669;
--color-warning-500: #f59e0b;
--color-danger-600: #dc2626;
--color-info-600: #2563eb;
```

### Typography

```css
font-family: 'Inter', system-ui, sans-serif;
--text-xs: 0.75rem;
--text-sm: 0.875rem;
--text-base: 1rem;
--text-lg: 1.125rem;
--text-xl: 1.25rem;
```

### Shadows

```css
--shadow-soft: 0 2px 15px -3px rgba(0, 0, 0, 0.07);
--shadow-medium: 0 4px 25px -5px rgba(0, 0, 0, 0.1);
--shadow-large: 0 10px 50px -12px rgba(0, 0, 0, 0.25);
```

## 🧩 Komponenty

### Button Component

**Lokalizacja**: `resources/views/components/ui/button.blade.php`

**Warianty**:
- `primary` - Główny przycisk akcji
- `secondary` - Drugorzędny przycisk
- `success` - Akcje pozytywne
- `warning` - Ostrzeżenia
- `danger` - Akcje destrukcyjne
- `warm` - Pet-friendly akcje
- `nature` - Eko/zdrowe akcje
- `outline` - Przycisk z obramowaniem
- `ghost` - Przycisk bez tła
- `link` - Przycisk linkowy

**Rozmiary**: `xs`, `sm`, `md`, `lg`, `xl`

**Przykład użycia**:
```blade
<x-ui.button variant="primary" size="lg" icon="search">
    Znajdź opiekuna
</x-ui.button>
```

### Input Component

**Lokalizacja**: `resources/views/components/ui/input.blade.php`

**Właściwości**:
- `label` - Etykieta pola
- `icon` - Ikona (user, email, search, phone, location, money)
- `error` - Komunikat błędu
- `hint` - Tekst pomocniczy
- `required` - Pole obowiązkowe
- `floating` - Floating label

**Przykład użycia**:
```blade
<x-ui.input
    label="Adres email"
    icon="email"
    required
    error="{{ $errors->first('email') }}"
    hint="Wpisz swój adres email"
/>
```

## 🎯 Ikony SVG

Wszystkie ikony zostały zastąpione profesjonalnymi ikonami SVG:

- **Emoji** ❌ → **SVG Icons** ✅
- **🐾 → Heart SVG** (logo)
- **📍 → Location SVG** (lokalizacja)
- **🔍 → Search SVG** (wyszukiwanie)
- **✓ → Check SVG** (potwierdzenia)

## 📱 Responsywność

### Breakpoints
- `sm`: 640px
- `md`: 768px
- `lg`: 1024px
- `xl`: 1280px

### Mobile-First Components
- Touch targets min 44px
- Flexible layouts z `flex-col sm:flex-row`
- Responsive spacing i typography

## 🎨 Styl Aplikacji

### Layout
- **Background**: Gradient primary-600 → warm-600
- **Cards**: Białe z backdrop-blur-md
- **Shadows**: Soft shadows dla depth
- **Radius**: Zaokrąglone 12px-24px

### Animacje
```css
--animation-fade-in: fadeIn 0.5s ease-in-out;
--animation-slide-up: slideUp 0.3s ease-out;
--animation-bounce-gentle: bounceGentle 2s infinite;
```

## 📋 Checklist Implementacji

### ✅ Zrealizowane
- [x] Design tokens w Tailwind config
- [x] Komponenty Button i Input
- [x] Redesign strony głównej
- [x] Aktualizacja formularzy auth
- [x] Zamiana emoji na ikony SVG
- [x] Responsive navigation
- [x] A11y improvements
- [x] Guest layout optimization
- [x] Search interface redesign
- [x] Complete SVG icon system

### 🎯 Następne kroki
- [ ] Dashboard components
- [ ] Advanced search filters UI
- [ ] User profile components
- [ ] Notification system
- [ ] Pet cards design system
- [ ] Booking flow components

## 🚀 Użycie

### Instalacja nowych styli
```bash
npm run build
```

### Rozwój
```bash
npm run dev
```

### Przykład pełnego formularza
```blade
<form class="space-y-6">
    <x-ui.input
        label="Imię i nazwisko"
        icon="user"
        required
        error="{{ $errors->first('name') }}"
    />

    <x-ui.input
        label="Email"
        icon="email"
        type="email"
        required
        error="{{ $errors->first('email') }}"
    />

    <x-ui.button variant="primary" size="lg" fullWidth="true">
        <svg class="w-4 h-4 mr-2">...</svg>
        Zarejestruj się
    </x-ui.button>
</form>
```

## 📊 Metryki

- **Bundle size**: 56.34 kB CSS
- **Accessibility**: WCAG 2.1 AA compliance
- **Performance**: Optimized builds
- **Components**: 2 base UI components + Guest layout
- **Icons**: 100% SVG, 0% emoji
- **Build time**: ~1.1s average
- **Kompresja gzip**: 9.43 kB (83% reduction)

## 🔧 Maintenance

- Regularne aktualizacje tokenów designu
- Testy komponentów w różnych kontekstach
- Monitoring performance metrics
- Feedback od użytkowników

---

**Wersja**: 1.0.0
**Data**: 2025-09-18
**Autor**: Claude Code Designer