{{-- Safety Feature Component --}}
@props(['title', 'description'])

<div class="flex items-start">
    <div class="flex-shrink-0">
        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
            <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-600 dark:text-green-400" />
        </div>
    </div>
    <div class="ml-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        <p class="text-gray-600 dark:text-gray-300">{{ $description }}</p>
    </div>
</div>