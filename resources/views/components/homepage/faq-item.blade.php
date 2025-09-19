{{-- FAQ Item Component --}}
@props(['question', 'answer'])

<div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
        {{ $question }}
    </h3>
    <p class="text-gray-600 dark:text-gray-300">
        {{ $answer }}
    </p>
</div>