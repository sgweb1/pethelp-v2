{{--
    Komponent breadcrumbs (okruszki nawigacyjne)

    Wyświetla ścieżkę nawigacyjną z opcjonalną ikoną i separatorami.
    Umożliwia łatwe poruszanie się po hierarchii stron aplikacji.

    @param array $items - Tablica elementów breadcrumbs
    @param string $separator - Separator między elementami (domyślnie '/')
    @param string $class - Dodatkowe klasy CSS
--}}

@props([
    'items' => [],
    'separator' => '/',
    'class' => ''
])

@php
    $baseClasses = 'flex items-center space-x-2 text-sm';
    $classes = $baseClasses . ($class ? ' ' . $class : '');
@endphp

@if(!empty($items))
<nav {{ $attributes->merge(['class' => $classes]) }} aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        @foreach($items as $index => $item)
            <li class="flex items-center">
                @if($index > 0)
                    <span class="mx-2 text-gray-400 dark:text-gray-500">{{ $separator }}</span>
                @endif

                @if(isset($item['url']) && $index < count($items) - 1)
                    {{-- Link breadcrumb --}}
                    <a
                        href="{{ $item['url'] }}"
                        class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 flex items-center"
                    >
                        @if(isset($item['icon']))
                            <span class="mr-1">{{ $item['icon'] }}</span>
                        @endif
                        {{ $item['title'] }}
                    </a>
                @else
                    {{-- Current page breadcrumb --}}
                    <span class="text-gray-900 dark:text-gray-100 font-medium flex items-center">
                        @if(isset($item['icon']))
                            <span class="mr-1">{{ $item['icon'] }}</span>
                        @endif
                        {{ $item['title'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif