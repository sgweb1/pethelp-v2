@props([
    'pet',
    'compact' => false
])

@php
$typeIcons = [
    'pies' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
    'kot' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
    'ptak' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2m4 0V2a1 1 0 011-1h4a1 1 0 011 1v2m-8 16l4-4-4-4m0 8l-4-4 4-4"></path></svg>',
    'inne' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>'
];

$typeIcon = $typeIcons[$pet->type] ?? $typeIcons['inne'];
@endphp

<div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-soft hover:shadow-medium transition-all duration-300 overflow-hidden group {{ $compact ? 'p-4' : 'p-6' }}">
    @if(!$compact)
        <!-- Pet Photo -->
        <div class="aspect-square rounded-xl overflow-hidden mb-4 bg-gradient-to-br from-warm-100 to-nature-100">
            @if($pet->photo_url)
                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                        <div class="text-primary-600">
                            {!! $typeIcon !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="space-y-3">
        <!-- Pet Info Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                @if($compact)
                    <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600">
                        {!! $typeIcon !!}
                    </div>
                @endif
                <div>
                    <h3 class="font-semibold text-gray-900 {{ $compact ? 'text-sm' : 'text-lg' }}">{{ $pet->name }}</h3>
                    <p class="text-xs text-gray-500">{{ ucfirst($pet->type) }}{{ $pet->breed ? " • {$pet->breed}" : '' }}</p>
                </div>
            </div>

            <!-- Status Badge -->
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pet->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-700' }}">
                {{ $pet->is_active ? 'Aktywny' : 'Nieaktywny' }}
            </span>
        </div>

        @if(!$compact)
            <!-- Pet Details -->
            <div class="grid grid-cols-2 gap-3 text-sm">
                @if($pet->gender)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-gray-600">{{ ucfirst($pet->gender) }}</span>
                    </div>
                @endif

                @if($pet->weight)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                        <span class="text-gray-600">{{ $pet->weight }} kg</span>
                    </div>
                @endif

                @if($pet->birth_date)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-600">{{ $pet->birth_date->format('d.m.Y') }}</span>
                    </div>
                @endif
            </div>

            @if($pet->description)
                <p class="text-sm text-gray-600 line-clamp-2">{{ $pet->description }}</p>
            @endif

            <!-- Medical Info Alert -->
            @if($pet->medical_info && count($pet->medical_info) > 0)
                <div class="flex items-center space-x-2 p-2 bg-warning-50 border border-warning-200 rounded-lg">
                    <svg class="w-4 h-4 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 15c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-xs text-warning-700">Wymaga specjalnej opieki medycznej</span>
                </div>
            @endif
        @endif

        <!-- Action Button -->
        <div class="pt-2">
            <x-ui.button variant="outline" size="sm" fullWidth="true">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ $compact ? 'Edytuj' : 'Zarządzaj profilem' }}
            </x-ui.button>
        </div>
    </div>
</div>