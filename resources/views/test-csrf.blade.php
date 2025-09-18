<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CSRF</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-container { max-width: 500px; margin: 0 auto; padding: 30px; border: 1px solid #ccc; border-radius: 10px; }
        input, button { padding: 10px; margin: 10px 0; width: 100%; }
        button { background: #007cba; color: white; border: none; cursor: pointer; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Test CSRF Token</h1>

        <div class="info">
            <strong>CSRF Token:</strong><br>
            <code>{{ csrf_token() }}</code>
        </div>

        <form method="POST" action="{{ route('test-csrf') }}">
            @csrf
            <label>Test formularz:</label>
            <input type="text" name="test" placeholder="Wpisz coś..." required>
            <button type="submit">Wyślij (z CSRF)</button>
        </form>

        <hr style="margin: 30px 0;">

        <form method="POST" action="{{ route('test-csrf') }}">
            <!-- Celowo bez @csrf -->
            <label>Test bez CSRF (powinien zwrócić błąd 419):</label>
            <input type="text" name="test" placeholder="Wpisz coś..." required>
            <button type="submit">Wyślij (bez CSRF)</button>
        </form>

        <hr style="margin: 30px 0;">

        <p><strong>Session ID:</strong> {{ session()->getId() }}</p>
        <p><strong>APP_KEY istnieje:</strong> {{ config('app.key') ? 'TAK' : 'NIE' }}</p>
        <p><strong>URL aplikacji:</strong> {{ config('app.url') }}</p>
    </div>
</body>
</html>