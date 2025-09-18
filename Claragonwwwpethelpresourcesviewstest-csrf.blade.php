<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CSRF Test</title>
</head>
<body>
    <h1>CSRF Test</h1>
    <p>Current CSRF Token: {{ csrf_token() }}</p>
    <p>Session ID: {{ session()->getId() }}</p>

    <form method="POST" action="{{ route('test-csrf') }}">
        @csrf
        <input type="text" name="test_field" value="test_value">
        <button type="submit">Test CSRF</button>
    </form>

    <hr>

    <button onclick="testAjax()">Test AJAX CSRF</button>

    <script>
        function testAjax() {
            fetch('{{ route("test-csrf") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    test_field: 'ajax_test'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                alert('AJAX Success: ' + JSON.stringify(data));
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('AJAX Error: ' + error);
            });
        }
    </script>
</body>
</html>
