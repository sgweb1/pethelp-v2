<div class="max-w-6xl mx-auto p-6">
    <!-- Header & Stats -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $this->user ? 'Recenzje dla ' . $this->user->name : 'Wszystkie recenzje' }}
                </h2>
                @if($this->user && $this->averageRating > 0)
                    <div class="flex items-center mt-2">
                        <div class="text-2xl mr-2">{{ str_repeat('⭐', round($this->averageRating)) }}</div>
                        <span class="text-lg font-semibold">{{ number_format($this->averageRating, 1) }}/5</span>
                        <span class="text-gray-600 ml-2">({{ array_sum($this->ratingDistribution) }} recenzji)</span>
                    </div>
                @endif
            </div>

            <div class="flex items-center space-x-3">
                <button wire:click="toggleShowAll"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $showAll ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $showAll ? 'Pokaż ostatnie' : 'Pokaż wszystkie' }}
                </button>
            </div>
        </div>

        <!-- Rating Distribution (only for user profiles) -->
        @if($this->user && array_sum($this->ratingDistribution) > 0)
            <div class="grid grid-cols-5 gap-2 mb-6">
                @for($i = 5; $i >= 1; $i--)
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600 mb-1">{{ $i }}⭐</div>
                        <div class="bg-gray-200 rounded-full h-2">
                            @php
                                $total = array_sum($this->ratingDistribution);
                                $percentage = $total > 0 ? ($this->ratingDistribution[$i] / $total) * 100 : 0;
                            @endphp
                            <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ $this->ratingDistribution[$i] }}</div>
                    </div>
                @endfor
            </div>
        @endif

        <!-- Filters -->
        <div class="flex flex-wrap gap-2">
            <button wire:click="filterByRating('')"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterRating === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Wszystkie oceny
            </button>
            @for($i = 5; $i >= 1; $i--)
                <button wire:click="filterByRating({{ $i }})"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterRating == $i ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $i }}⭐
                </button>
            @endfor
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Reviews List -->
    <div class="space-y-6">
        @forelse($this->reviews as $review)
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Reviewer Avatar/Initial -->
                        <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($review->reviewer->name, 0, 1) }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $review->reviewer->name }}</h4>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <span>{{ $review->created_at->format('d.m.Y') }}</span>
                                        <span>•</span>
                                        <span>{{ $review->booking->service->title }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg">{{ $review->stars }}</div>
                                    <div class="text-sm text-gray-600">{{ $review->rating_label }}</div>
                                </div>
                            </div>

                            @if($review->comment)
                                <p class="text-gray-700 mb-3">{{ $review->comment }}</p>
                            @endif

                            <!-- Service Info -->
                            <div class="bg-gray-50 rounded-lg p-3 text-sm">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                    <div>
                                        <span class="font-medium text-gray-600">Usługa:</span>
                                        <div>{{ $review->booking->service->title }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">Data:</span>
                                        <div>{{ $review->booking->start_date->format('d.m.Y') }}</div>
                                    </div>
                                    @if(!$this->user)
                                        <div>
                                            <span class="font-medium text-gray-600">Recenzowany:</span>
                                            <div>{{ $review->reviewee->name }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(Auth::check())
                        <div class="flex items-center space-x-2 ml-4">
                            @if($review->canBeDeletedBy(Auth::user()))
                                <button wire:click="deleteReview({{ $review->id }})"
                                        class="text-gray-500 hover:text-red-600 transition-colors"
                                        title="Usuń recenzję"
                                        onclick="return confirm('Czy na pewno chcesz usunąć tę recenzję?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif

                            @if($review->reviewee_id === Auth::id() || Auth::user()->isAdmin())
                                <button wire:click="hideReview({{ $review->id }})"
                                        class="text-gray-500 hover:text-yellow-600 transition-colors"
                                        title="Ukryj recenzję"
                                        onclick="return confirm('Czy na pewno chcesz ukryć tę recenzję?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">⭐</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak recenzji</h3>
                <p class="text-gray-600">
                    @if($this->user)
                        {{ $this->user->name }} nie ma jeszcze żadnych recenzji.
                    @else
                        Nie znaleziono recenzji spełniających kryteria.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->reviews->hasPages())
        <div class="mt-8">
            {{ $this->reviews->links() }}
        </div>
    @endif
</div>
