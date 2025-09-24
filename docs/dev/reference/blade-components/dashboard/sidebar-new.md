# Blade Component: dashboard\sidebar-new

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla dashboard\sidebar-new.

## Lokalizacja
- **Plik**: `resources/views/components/dashboard\sidebar-new.blade.php`
- **Użycie**: `<x-dashboard\sidebar.new>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$activeSection** - Slot dla activeSection

## CSS Classes
```css
.bg-white dark:bg-gray-800 shadow-sm border-r border-gray-200 dark:border-gray-700 min-h-screen w-64 fixed left-0 top-0 z-40 transform transition-transform duration-300 ease-in-out
.sidebarOpen ? 
.{ 
.flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700
.flex items-center
.w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center mr-3
.text-white font-bold text-sm
.text-lg font-semibold text-gray-900 dark:text-white
.lg:hidden text-gray-500 hover:text-gray-700
.w-6 h-6
.py-4 overflow-y-auto
.px-4 mb-6
.flex items-center p-3 rounded-lg {{ $activeSection === 
.w-5 h-5 mr-3
.font-medium
.space-y-2
.px-4
.flex items-center justify-between w-full p-3 rounded-lg text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200
.w-4 h-4 transition-transform duration-200
.ml-8 mt-2 space-y-1
.block p-2 text-sm {{ $activeSection === 
.block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded transition-colors
.block p-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white
.absolute bottom-0 left-0 right-0 p-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700
.w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3
.text-gray-600 text-xs font-medium
.flex-1 min-w-0
.text-sm font-medium text-gray-900 dark:text-white truncate
.text-xs text-gray-500 dark:text-gray-400 truncate
.lg:hidden fixed top-4 left-4 z-50 p-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700
.w-6 h-6 text-gray-600 dark:text-gray-300
.lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30
```

## Usage Example
```blade
<x-dashboard\sidebar.new
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-dashboard\sidebar.new>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*