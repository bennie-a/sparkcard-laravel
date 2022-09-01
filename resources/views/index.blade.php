
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/diamond.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>SPARKCARD-Laravel</title>
</head>

<body>
    <div id="app">
    </div>
</body>
</html>