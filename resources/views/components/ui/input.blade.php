@props([
    'type' => 'text',
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'floating' => false
])

@php
$sizes = [
    'sm' => 'px-3 py-2 text-sm h-9',
    'md' => 'px-4 py-3 text-sm h-11',
    'lg' => 'px-4 py-3.5 text-base h-12',
];

$hasError = !empty($error);
$id = $attributes->get('id') ?? 'input_' . uniqid();

$inputClasses = collect([
    'block w-full rounded-xl border shadow-soft transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-offset-1',
    'placeholder-gray-400',
    $hasError ? 'border-danger-300 focus:border-danger-500 focus:ring-danger-200' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-200',
    $disabled ? 'bg-gray-100 cursor-not-allowed text-gray-500' : 'bg-white',
    $icon ? ($iconPosition === 'left' ? 'pl-11' : 'pr-11') : '',
    $sizes[$size] ?? $sizes['md'],
])->filter()->implode(' ');
@endphp

<div {{ $attributes->only('class') }}>
    @if($label && !$floating)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-danger-500 ml-1" aria-label="pole wymagane">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 {{ $iconPosition === 'left' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @switch($icon)
                        @case('user')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            @break
                        @case('email')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            @break
                        @case('search')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            @break
                        @case('phone')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            @break
                        @case('location')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            @break
                        @case('money')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            @break
                        @default
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    @endswitch
                </svg>
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $id }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $required ? 'required' : '' }}
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if($hasError) aria-describedby="{{ $id }}-error" @endif
            @if($hint && !$hasError) aria-describedby="{{ $id }}-hint" @endif
            {{ $attributes->except(['class', 'id'])->merge(['class' => $inputClasses]) }}
        />

        @if($floating && $label)
            <label for="{{ $id }}" class="absolute left-4 -top-2.5 bg-white px-2 text-sm font-medium text-gray-700 transition-all duration-200">
                {{ $label }}
                @if($required)
                    <span class="text-danger-500 ml-1">*</span>
                @endif
            </label>
        @endif
    </div>

    @if($hint && !$hasError)
        <p id="{{ $id }}-hint" class="mt-2 text-sm text-gray-600">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p id="{{ $id }}-error" class="mt-2 text-sm text-danger-600 flex items-center" role="alert">
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>