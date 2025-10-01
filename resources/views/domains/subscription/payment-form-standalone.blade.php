<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Przekierowanie do PayU - PetHelp</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }
        .logo {
            width: 64px;
            height: 64px;
            background-color: #4f46e5;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .logo svg {
            width: 32px;
            height: 32px;
            color: white;
        }
        h1 {
            color: #111827;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        p {
            color: #6b7280;
            margin-bottom: 32px;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 32px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .button {
            display: none;
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-bottom: 16px;
        }
        .button:hover {
            background-color: #4338ca;
        }
        .small-text {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 32px;
        }
        .security-info {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            align-items: center;
        }
        .security-icon {
            width: 20px;
            height: 20px;
            color: #10b981;
            margin-right: 12px;
        }
        .security-text {
            text-align: left;
        }
        .security-title {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin: 0;
        }
        .security-desc {
            font-size: 12px;
            color: #9ca3af;
            margin: 0;
        }
        .debug {
            margin-top: 20px;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 8px;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>

        <h1>Przekierowanie do PayU</h1>
        <p>Za chwilę zostaniesz przekierowany do bezpiecznej strony płatności PayU</p>

        <div class="spinner"></div>

        <!-- PayU Form - completely standalone -->
        <form id="payu-form" method="POST" action="{{ $redirectUrl }}" style="display: none;">
            @foreach($formData as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>

        <button id="manual-submit" class="button" onclick="submitForm()">
            Przejdź do płatności
        </button>

        <p class="small-text">
            Jeśli nie zostaniesz automatycznie przekierowany w ciągu 10 sekund, kliknij przycisk powyżej
        </p>

        <div class="security-info">
            <svg class="security-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            <div class="security-text">
                <p class="security-title">Bezpieczne połączenie SSL</p>
                <p class="security-desc">Twoje dane są chronione przez PayU</p>
            </div>
        </div>

        @if(config('app.debug'))
        <div class="debug">
            <strong>Debug Info:</strong><br>
            PayU URL: {{ $redirectUrl }}<br>
            Form fields: {{ count($formData) }}<br>
            @foreach(array_slice($formData, 0, 5) as $key => $value)
                {{ $key }}: {{ Str::limit($value, 30) }}<br>
            @endforeach
            @if(count($formData) > 5)
                ... and {{ count($formData) - 5 }} more fields
            @endif
        </div>
        @endif
    </div>

    <script>
        // Completely vanilla JavaScript - no jQuery, no Livewire, no interference
        console.log('Standalone PayU payment form loaded');
        console.log('PayU URL:', '{{ $redirectUrl }}');

        @if(config('app.debug'))
        console.log('Form Data:', @json($formData));
        @endif

        function submitForm() {
            console.log('Manual form submission triggered');
            const form = document.getElementById('payu-form');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);

            @if(config('app.debug'))
            const formData = new FormData(form);
            console.log('Form fields:');
            for (let [key, value] of formData.entries()) {
                console.log('  ' + key + ':', value);
            }
            @endif

            form.submit();
        }

        // Auto-submit after 3 seconds
        setTimeout(function() {
            console.log('Auto-submitting form to PayU...');
            submitForm();
        }, 3000);

        // Show manual button after 10 seconds
        setTimeout(function() {
            document.getElementById('manual-submit').style.display = 'block';
        }, 10000);
    </script>
</body>
</html>