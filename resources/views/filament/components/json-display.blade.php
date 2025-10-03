{{--
    Komponent wyświetlania danych JSON w czytelnej formie

    Formatuje dane JSON jako czytelną listę par klucz-wartość
    z odpowiednim stylowaniem dla panelu Filament.

    @param array $data - Dane do wyświetlenia
--}}
<div class="space-y-2">
    @if(empty($data))
        <p class="text-sm text-gray-500 dark:text-gray-400">Brak danych</p>
    @else
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
            <dl class="grid grid-cols-1 gap-3">
                @foreach($data as $key => $value)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-2 last:border-0 last:pb-0">
                        <dt class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $key }}
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                            @if(is_array($value) || is_object($value))
                                <pre class="bg-white dark:bg-gray-900 p-2 rounded border border-gray-200 dark:border-gray-600 text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @elseif(is_bool($value))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $value ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                    {{ $value ? 'Tak' : 'Nie' }}
                                </span>
                            @elseif(is_null($value))
                                <span class="text-gray-400 dark:text-gray-500 italic">null</span>
                            @else
                                {{ $value }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif
</div>
