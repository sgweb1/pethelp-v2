<div class="h-full bg-gray-100 dark:bg-gray-900"
     x-data="{ debug: {{ config('app.debug') ? 'true' : 'false' }} }"
     x-init="
     console.log('ChatApp loaded:', { autoStartUserId: {{ $autoStartUserId ?? 'null' }}, autoStartBookingId: {{ $autoStartBookingId ?? 'null' }} });
     @if($autoStartUserId)
     console.log('Dispatching startConversationWith event...');
     $dispatch('startConversationWith', { userId: {{ $autoStartUserId }}, bookingId: {{ $autoStartBookingId ?? 'null' }} });
     @else
     console.log('No autoStartUserId - event not dispatched');
     @endif
     "
    @if($fullWidth)
        <div class="flex h-full">
    @else
        <div class="max-w-7xl mx-auto h-full">
            <div class="flex h-full">
    @endif
            <!-- Conversations Sidebar -->
            <div class="w-1/3 border-r border-gray-200 bg-white">
                <livewire:conversation-list />
            </div>

            <!-- Chat Window -->
            <div class="flex-1">
                <livewire:chat-window />
            </div>
    @if($fullWidth)
        </div>
    @else
            </div>
        </div>
    @endif
</div>
