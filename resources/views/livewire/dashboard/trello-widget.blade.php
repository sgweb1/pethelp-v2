{{-- Trello Integration Widget --}}
<div class="bg-white/95 backdrop-blur-md rounded-xl p-6 shadow-lg">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.3 3 3 3h18c1.7 0 3-1.3 3-3V3c0-1.7-1.3-3-3-3zM10.5 17.25c0 .414-.336.75-.75.75h-6c-.414 0-.75-.336-.75-.75V5.25c0-.414.336-.75.75-.75h6c.414 0 .75.336.75.75v12zm10.5-4.5c0 .414-.336.75-.75.75h-6c-.414 0-.75-.336-.75-.75V5.25c0-.414.336-.75.75-.75h6c.414 0 .75.336.75.75v7.5z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Project Progress</h3>
                <p class="text-sm text-gray-600">Trello Integration</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if($isConfigured && $boardUrl)
                <a href="{{ $boardUrl }}" target="_blank"
                   class="text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors"
                   title="Open Trello Board">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Open Board
                </a>
            @endif

            <button wire:click="refreshData"
                    wire:loading.attr="disabled"
                    class="text-gray-500 hover:text-gray-700 transition-colors"
                    title="Refresh Data">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     wire:loading.class="animate-spin">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    @if(!$isConfigured)
        {{-- Not Configured State --}}
        <div class="text-center py-8">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h4 class="text-lg font-medium text-gray-900 mb-2">Trello Not Configured</h4>
            <p class="text-gray-600 mb-4">Setup Trello integration to track project progress</p>
            <div class="space-y-2 text-sm text-gray-600">
                <p>1. Get API credentials from <a href="https://trello.com/app-key" target="_blank" class="text-blue-600 hover:text-blue-700">trello.com/app-key</a></p>
                <p>2. Add TRELLO_API_KEY and TRELLO_TOKEN to .env</p>
                <p>3. Run: <code class="bg-gray-100 px-2 py-1 rounded">php artisan trello:setup</code></p>
            </div>
        </div>
    @elseif($isLoading)
        {{-- Loading State --}}
        <div class="text-center py-8">
            <div class="animate-spin mx-auto w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full mb-4"></div>
            <p class="text-gray-600">Loading Trello data...</p>
        </div>
    @elseif(empty($stats))
        {{-- No Data State --}}
        <div class="text-center py-8">
            <div class="mx-auto w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-lg font-medium text-gray-900 mb-2">No Data Available</h4>
            <p class="text-gray-600 mb-4">Unable to load Trello board data</p>
            <button wire:click="refreshData"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Try Again
            </button>
        </div>
    @else
        {{-- Main Content --}}

        {{-- Progress Overview --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                <span class="text-sm text-gray-600">{{ $this->progressPercentage }}% Complete</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
                     style="width: {{ $this->progressPercentage }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>{{ ($stats['done'] ?? 0) + ($stats['deployed'] ?? 0) }} completed</span>
                <span>{{ $this->totalTasks }} total</span>
            </div>
        </div>

        {{-- Task Distribution --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            {{-- Backlog --}}
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['productbacklog'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">📋 Backlog</div>
            </div>

            {{-- In Progress --}}
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                    {{ ($stats['sprintbacklog'] ?? 0) + ($stats['indevelopment'] ?? 0) + ($stats['testing'] ?? 0) + ($stats['codereview'] ?? 0) }}
                </div>
                <div class="text-xs text-gray-500">🔄 Active</div>
            </div>

            {{-- Done --}}
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ ($stats['done'] ?? 0) + ($stats['deployed'] ?? 0) }}</div>
                <div class="text-xs text-gray-500">✅ Done</div>
            </div>
        </div>

        {{-- Detailed Stats --}}
        <div class="grid grid-cols-2 gap-3 mb-6">
            @if(isset($stats['sprintbacklog']) && $stats['sprintbacklog'] > 0)
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <span class="text-lg mr-2">🎯</span>
                    <span class="text-sm font-medium text-gray-700">Sprint</span>
                </div>
                <span class="text-lg font-bold text-blue-600">{{ $stats['sprintbacklog'] }}</span>
            </div>
            @endif

            @if(isset($stats['indevelopment']) && $stats['indevelopment'] > 0)
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                <div class="flex items-center">
                    <span class="text-lg mr-2">👨‍💻</span>
                    <span class="text-sm font-medium text-gray-700">Development</span>
                </div>
                <span class="text-lg font-bold text-orange-600">{{ $stats['indevelopment'] }}</span>
            </div>
            @endif

            @if(isset($stats['testing']) && $stats['testing'] > 0)
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div class="flex items-center">
                    <span class="text-lg mr-2">🧪</span>
                    <span class="text-sm font-medium text-gray-700">Testing</span>
                </div>
                <span class="text-lg font-bold text-yellow-600">{{ $stats['testing'] }}</span>
            </div>
            @endif

            @if(isset($stats['codereview']) && $stats['codereview'] > 0)
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                <div class="flex items-center">
                    <span class="text-lg mr-2">📝</span>
                    <span class="text-sm font-medium text-gray-700">Review</span>
                </div>
                <span class="text-lg font-bold text-purple-600">{{ $stats['codereview'] }}</span>
            </div>
            @endif
        </div>

        {{-- Recent Activity --}}
        @if(!empty($recentActivity))
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Activity</h4>
            <div class="space-y-2">
                @foreach($recentActivity as $activity)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center flex-1 min-w-0">
                        <span class="text-xs text-gray-500 mr-2">{{ $activity['time'] }}</span>
                        <span class="truncate">{{ $activity['title'] }}</span>
                    </div>
                    <span class="text-xs text-gray-500 ml-2">{{ $activity['list'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endif

    {{-- Flash Messages --}}
    @if(session('trello_success'))
        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">{{ session('trello_success') }}</p>
        </div>
    @endif

    @if(session('trello_error'))
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700">{{ session('trello_error') }}</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('trello-refreshed', () => {
            // You can add any additional client-side logic here
            console.log('Trello data refreshed');
        });
    });
</script>
@endpush