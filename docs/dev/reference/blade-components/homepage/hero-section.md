# Blade Component: homepage\hero-section

Hero Section - Pet Sitters CORE Focus

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\hero-section.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\hero-section.blade.php`
- **Użycie**: `<x-homepage\hero.section>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.hero-gradient min-h-screen relative overflow-hidden
.absolute inset-0 opacity-10
.w-full h-full
.relative z-10 flex items-center min-h-screen
.max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full
.grid lg:grid-cols-2 gap-12 items-center
.text-center lg:text-left
.inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 mb-8
.text-white/90 text-sm font-medium
.text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight
.text-yellow-300
.text-xl text-white/90 mb-8 leading-relaxed
.grid grid-cols-2 gap-3 mb-10
.feature-pill rounded-xl px-4 py-3 text-white text-sm font-medium flex items-center hover:bg-white/20 transition-colors cursor-pointer
.text-lg mr-2
.flex flex-wrap justify-center lg:justify-start gap-6 mb-10
.flex items-center text-white/90
.w-5 h-5 text-green-400 mr-2
.text-sm font-medium
.flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-8
.group bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl text-center touch-friendly
.flex items-center justify-center
.w-5 h-5 mr-2 group-hover:animate-pulse
.group bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 border-2 border-white/20 hover:border-white/40 text-center backdrop-blur-md touch-friendly
.bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 mb-8
.flex items-center justify-between
.flex items-center
.w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center mr-3
.w-5 h-5 text-gray-900
.text-white font-semibold text-sm
.text-white/70 text-xs
.text-yellow-400 font-bold text-lg
.pt-8 border-t border-white/20
.grid grid-cols-3 gap-6 text-center
.group cursor-pointer
.text-2xl sm:text-3xl font-bold text-white mb-1 group-hover:text-yellow-300 transition-colors
.text-white/70 text-sm
.hidden lg:block
.relative
.aspect-square rounded-3xl overflow-hidden shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-700 bg-gradient-to-br from-blue-400 to-purple-500
.w-full h-full flex items-center justify-center text-white text-6xl
.absolute -top-4 -left-4 bg-white rounded-xl p-4 shadow-lg animate-float-slow hover:shadow-2xl transition-shadow cursor-pointer
.w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3
.w-4 h-4 text-green-600
.text-sm font-semibold text-gray-900
.text-xs text-gray-600
.absolute -bottom-4 -right-4 bg-white rounded-xl p-4 shadow-lg animate-float-fast hover:shadow-2xl transition-shadow cursor-pointer
.w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3
.w-4 h-4 text-blue-600
.absolute top-1/2 -left-8 bg-white rounded-xl p-3 shadow-lg animate-pulse
.w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-2
.w-3 h-3 text-yellow-600
.text-xs font-semibold text-gray-900
.absolute top-1/2 -right-8 bg-white rounded-xl p-3 shadow-lg
.w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mr-2
.w-3 h-3 text-purple-600
.absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/60 animate-bounce cursor-pointer
.block
.w-6 h-6
```

## Usage Example
```blade
<x-homepage\hero.section
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\hero.section>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*