<div class="h-screen bg-gray-100"
     @if($autoStartUserId)
     wire:init="$dispatch('startConversationWith', { userId: {{ $autoStartUserId }}, bookingId: {{ $autoStartBookingId ?? 'null' }} })"
     @endif>
    <div class="max-w-7xl mx-auto h-full">
        <div class="flex h-full">
            <!-- Conversations Sidebar -->
            <div class="w-1/3 border-r border-gray-200 bg-white">
                <livewire:conversation-list />
            </div>

            <!-- Chat Window -->
            <div class="flex-1">
                <livewire:chat-window />
            </div>
        </div>
    </div>
</div>
