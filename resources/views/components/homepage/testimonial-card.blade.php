{{-- Testimonial Card Component --}}
@props(['name', 'location', 'avatar', 'review'])

<div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
    <div class="flex items-center mb-4">
        <div class="flex text-yellow-400">
            <x-icon name="heroicon-s-star" class="w-5 h-5" />
            <x-icon name="heroicon-s-star" class="w-5 h-5" />
            <x-icon name="heroicon-s-star" class="w-5 h-5" />
            <x-icon name="heroicon-s-star" class="w-5 h-5" />
            <x-icon name="heroicon-s-star" class="w-5 h-5" />
        </div>
    </div>
    <p class="text-gray-600 dark:text-gray-300 mb-4">
        "{{ $review }}"
    </p>
    <div class="flex items-center">
        <div class="w-10 h-10 rounded-full mr-3 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
            {{ substr($name, 0, 1) }}{{ substr(explode(' ', $name)[1] ?? '', 0, 1) }}
        </div>
        <div>
            <div class="font-semibold text-gray-900 dark:text-white">{{ $name }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $location }}</div>
        </div>
    </div>
</div>