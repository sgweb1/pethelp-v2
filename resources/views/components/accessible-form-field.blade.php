@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'error' => null,
    'helpText' => null,
    'id' => null,
    'placeholder' => '',
    'value' => ''
])

@php
$fieldId = $id ?? "field-{$name}-" . uniqid();
$errorId = "error-{$fieldId}";
$helpId = "help-{$fieldId}";

$inputClasses = 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200';
$inputClasses .= $error ? ' border-red-500 bg-red-50 dark:bg-red-900/20' : ' border-gray-300 dark:border-gray-600';
$inputClasses .= ' bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400';

$ariaDescribedBy = collect([$error ? $errorId : null, $helpText ? $helpId : null])->filter()->implode(' ');
@endphp

<div class="form-field-accessible">
    @if($label)
        <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="required text-red-500" aria-label="wymagane">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($type === 'textarea')
            <textarea
                id="{{ $fieldId }}"
                name="{{ $name }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                @if($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
                @if($error) aria-invalid="true" @endif
                @if($required) aria-required="true" @endif
                class="{{ $inputClasses }} min-h-[100px]"
                {{ $attributes->except(['class']) }}
            >{{ $value }}</textarea>
        @elseif($type === 'select')
            <select
                id="{{ $fieldId }}"
                name="{{ $name }}"
                {{ $required ? 'required' : '' }}
                @if($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
                @if($error) aria-invalid="true" @endif
                @if($required) aria-required="true" @endif
                class="{{ $inputClasses }}"
                {{ $attributes->except(['class']) }}
            >
                {{ $slot }}
            </select>
        @else
            <input
                type="{{ $type }}"
                id="{{ $fieldId }}"
                name="{{ $name }}"
                placeholder="{{ $placeholder }}"
                value="{{ $value }}"
                {{ $required ? 'required' : '' }}
                @if($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
                @if($error) aria-invalid="true" @endif
                @if($required) aria-required="true" @endif
                class="{{ $inputClasses }}"
                {{ $attributes->except(['class']) }}
            />
        @endif
    </div>

    @if($error)
        <div id="{{ $errorId }}" class="error-message mt-1 text-sm text-red-600 dark:text-red-400" role="alert">
            <span aria-hidden="true">⚠</span>
            {{ $error }}
        </div>
    @endif

    @if($helpText)
        <div id="{{ $helpId }}" class="help-text mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $helpText }}
        </div>
    @endif
</div>