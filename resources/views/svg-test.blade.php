<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SVG.js Test - Bezpieczne renderowanie ikon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-gray-900">Test SafeSVGIcons System</h1>

        {{-- Test różnych ikon --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-medium mb-2">Loading Spinner</h3>
                <x-ui.safe-icon icon="loading" size="24x24" classes="animate-spin text-blue-500" />
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-medium mb-2">Check Mark</h3>
                <x-ui.safe-icon icon="check" size="24x24" classes="text-green-500" />
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-medium mb-2">Close</h3>
                <x-ui.safe-icon icon="close" size="24x24" classes="text-red-500" />
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-medium mb-2">Arrow Right</h3>
                <x-ui.safe-icon icon="arrowRight" size="24x24" classes="text-purple-500" />
            </div>
        </div>

        {{-- Test bezpośrednio przez data atrybuty --}}
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-semibold mb-4">Bezpośredni test data-atrybutów:</h2>
            <div class="flex gap-4 items-center">
                <div data-svg-icon="loading" data-svg-size="20x20" data-svg-classes="animate-spin text-blue-600"></div>
                <span>Loading...</span>

                <div data-svg-icon="check" data-svg-size="20x20" data-svg-classes="text-green-600"></div>
                <span>Success</span>

                <div data-svg-icon="location" data-svg-size="20x20" data-svg-classes="text-red-600"></div>
                <span>Location</span>
            </div>
        </div>

        {{-- Test różnych rozmiarów --}}
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-semibold mb-4">Test różnych rozmiarów:</h2>
            <div class="flex gap-4 items-end">
                <div>
                    <p class="text-sm text-gray-600 mb-1">12x12</p>
                    <div data-svg-icon="settings" data-svg-size="12x12" data-svg-classes="text-gray-700"></div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">16x16</p>
                    <div data-svg-icon="settings" data-svg-size="16x16" data-svg-classes="text-gray-700"></div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">20x20</p>
                    <div data-svg-icon="settings" data-svg-size="20x20" data-svg-classes="text-gray-700"></div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">24x24</p>
                    <div data-svg-icon="settings" data-svg-size="24x24" data-svg-classes="text-gray-700"></div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">32x32</p>
                    <div data-svg-icon="settings" data-svg-size="32x32" data-svg-classes="text-gray-700"></div>
                </div>
            </div>
        </div>

        {{-- Test programmatic dodawania --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Test programmatic dodawania:</h2>
            <div class="flex gap-4 mb-4">
                <button id="add-spinner" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Dodaj Spinner
                </button>
                <button id="add-check" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Dodaj Check
                </button>
                <button id="clear-icons" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Wyczyść
                </button>
            </div>
            <div id="dynamic-icons" class="flex gap-2 p-4 border-2 border-dashed border-gray-300 rounded min-h-[60px]">
                {{-- Tutaj będą dynamicznie dodawane ikony --}}
            </div>
        </div>

        {{-- Console debug info --}}
        <div class="mt-8 bg-gray-900 text-green-400 p-4 rounded font-mono text-sm">
            <div>Console Debug:</div>
            <div id="debug-console" class="mt-2 max-h-32 overflow-y-auto">
                <!-- JavaScript wypełni to debugiem -->
            </div>
        </div>
    </div>

    <script>
        // Test programmatic API
        document.getElementById('add-spinner').addEventListener('click', () => {
            const container = document.getElementById('dynamic-icons');
            const iconDiv = document.createElement('div');

            // Debug info
            const debugConsole = document.getElementById('debug-console');
            debugConsole.innerHTML += '<div>Creating loading spinner...</div>';

            if (window.SafeSVGIcons) {
                window.SafeSVGIcons.createLoadingSpinner(iconDiv, {
                    classes: 'animate-spin text-purple-500',
                    size: { width: 24, height: 24 }
                });
                container.appendChild(iconDiv);
                debugConsole.innerHTML += '<div>✓ Spinner created successfully</div>';
            } else {
                debugConsole.innerHTML += '<div>❌ SafeSVGIcons not available</div>';
            }
            debugConsole.scrollTop = debugConsole.scrollHeight;
        });

        document.getElementById('add-check').addEventListener('click', () => {
            const container = document.getElementById('dynamic-icons');
            const iconDiv = document.createElement('div');

            const debugConsole = document.getElementById('debug-console');
            debugConsole.innerHTML += '<div>Creating check icon...</div>';

            if (window.SafeSVGIcons) {
                window.SafeSVGIcons.createIcon('check', iconDiv, {
                    classes: 'text-green-500',
                    size: { width: 24, height: 24 }
                });
                container.appendChild(iconDiv);
                debugConsole.innerHTML += '<div>✓ Check icon created successfully</div>';
            } else {
                debugConsole.innerHTML += '<div>❌ SafeSVGIcons not available</div>';
            }
            debugConsole.scrollTop = debugConsole.scrollHeight;
        });

        document.getElementById('clear-icons').addEventListener('click', () => {
            const container = document.getElementById('dynamic-icons');
            container.innerHTML = '';

            const debugConsole = document.getElementById('debug-console');
            debugConsole.innerHTML += '<div>Icons cleared</div>';
            debugConsole.scrollTop = debugConsole.scrollHeight;
        });

        // Debug informacje przy ładowaniu
        document.addEventListener('DOMContentLoaded', () => {
            const debugConsole = document.getElementById('debug-console');
            debugConsole.innerHTML += '<div>DOM loaded</div>';
            debugConsole.innerHTML += '<div>SafeSVGIcons available: ' + (window.SafeSVGIcons ? 'YES' : 'NO') + '</div>';

            setTimeout(() => {
                const iconElements = document.querySelectorAll('[data-svg-icon]');
                debugConsole.innerHTML += '<div>Found ' + iconElements.length + ' icon elements</div>';
                debugConsole.scrollTop = debugConsole.scrollHeight;
            }, 1000);
        });
    </script>
</body>
</html>