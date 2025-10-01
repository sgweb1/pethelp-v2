@props(['on'])

<div x-data="{
        shown: false,
        timeout: null,
        showMessage() {
            clearTimeout(this.timeout);
            this.shown = true;
            this.timeout = setTimeout(() => { this.shown = false }, 2000);
        }
    }"
     x-init="@this.on('{{ $on }}', () => this.showMessage())"
     x-show.transition.out.opacity.duration.1500ms="shown"
     x-transition:leave.opacity.duration.1500ms
     style="display: none;"
    {{ $attributes->merge(['class' => 'text-sm text-gray-600']) }}>
    {{ $slot->isEmpty() ? __('Saved.') : $slot }}
</div>
