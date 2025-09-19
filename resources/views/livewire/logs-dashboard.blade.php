<div class="p-6 max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Logów JavaScript</h1>
        <p class="text-gray-600">Monitorowanie błędów i logów z aplikacji frontend</p>
    </div>

    {{-- Statystyki --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-600">Łącznie logów</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-red-600">{{ $stats['errors'] }}</div>
            <div class="text-sm text-gray-600">Błędy</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['warnings'] }}</div>
            <div class="text-sm text-gray-600">Ostrzeżenia</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['sessions'] }}</div>
            <div class="text-sm text-gray-600">Sesje</div>
        </div>
    </div>

    {{-- Kontrolki --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            {{-- Data --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                <input
                    wire:model.live="selectedDate"
                    type="date"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm"
                />
            </div>

            {{-- Filtr typu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Typ</label>
                <select wire:model.live="filterType" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="all">Wszystkie</option>
                    @foreach($logTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Wyszukiwanie --}}
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Szukaj</label>
                <input
                    wire:model.live.debounce.300ms="searchQuery"
                    type="text"
                    placeholder="Wyszukaj w logach..."
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                />
            </div>

            {{-- Akcje --}}
            <div class="flex gap-2">
                <button
                    wire:click="loadLogs"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                >
                    Odśwież
                </button>

                <label class="flex items-center gap-2 text-sm">
                    <input
                        wire:model.live="autoRefresh"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    Auto-odświeżanie
                </label>

                @if(count($logs) > 0)
                    <button
                        wire:click="clearLogs"
                        wire:confirm="Czy na pewno chcesz usunąć wszystkie logi z tego dnia?"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm"
                    >
                        Wyczyść
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Lista logów --}}
    <div class="bg-white rounded-lg shadow">
        @if(count($filteredLogs) > 0)
            <div class="divide-y divide-gray-200">
                @foreach($filteredLogs as $log)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                {{-- Typ i czas --}}
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($log['type'] === 'javascript_error' || $log['type'] === 'console_error')
                                            bg-red-100 text-red-800
                                        @elseif($log['type'] === 'console_warn')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($log['type'] === 'promise_rejection')
                                            bg-purple-100 text-purple-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $log['type'])) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ date('H:i:s', strtotime($log['timestamp'])) }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        Sesja: {{ substr($log['session_id'], -8) }}
                                    </span>
                                </div>

                                {{-- Wiadomość --}}
                                <div class="text-sm text-gray-900 mb-2 font-mono">
                                    {{ $log['message'] }}
                                </div>

                                {{-- URL i plik --}}
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div>
                                        <strong>URL:</strong> {{ $log['url'] }}
                                    </div>
                                    @if(!empty($log['filename']))
                                        <div>
                                            <strong>Plik:</strong> {{ $log['filename'] }}
                                            @if(!empty($log['line']))
                                                (linia {{ $log['line'] }}{{ !empty($log['column']) ? ', kolumna ' . $log['column'] : '' }})
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Stack trace --}}
                                @if(!empty($log['stack']))
                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-xs text-blue-600 hover:text-blue-800">
                                            Pokaż stack trace
                                        </summary>
                                        <pre class="mt-2 text-xs bg-gray-100 p-2 rounded overflow-x-auto whitespace-pre-wrap">{{ $log['stack'] }}</pre>
                                    </details>
                                @endif

                                {{-- Dodatkowe dane --}}
                                @if(!empty($log['data']))
                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-xs text-blue-600 hover:text-blue-800">
                                            Dodatkowe dane
                                        </summary>
                                        <pre class="mt-2 text-xs bg-gray-100 p-2 rounded overflow-x-auto">{{ json_encode($log['data'], JSON_PRETTY_PRINT) }}</pre>
                                    </details>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                @if(count($logs) === 0)
                    Brak logów dla wybranej daty.
                @else
                    Brak logów pasujących do filtrów.
                @endif
            </div>
        @endif
    </div>

    @if(count($filteredLogs) > 0)
        <div class="mt-4 text-center text-sm text-gray-600">
            Wyświetlono {{ count($filteredLogs) }} z {{ count($logs) }} logów
        </div>
    @endif

    {{-- Auto-refresh script --}}
    @script
    <script>
        setInterval(() => {
            if ($wire.autoRefresh) {
                $wire.dispatch('log-received');
            }
        }, 10000); // Odświeżaj co 10 sekund
    </script>
    @endscript
</div>
