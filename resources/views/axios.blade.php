
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>axiosを学ぶ</title>
</head>

<body>
axiosを学ぶ
    <div id="app">
        <axios></axios>
    </div>
        <script src="{{ asset('js/app.js') }}" defer="defer"></script>
</body>
</html>