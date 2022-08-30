
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>axiosを学ぶ</title>
</head>

<body>
axiosを学ぶ
    <div id="app">
        <span v-text="greeting"></span>
    </div>
</body>
</html>