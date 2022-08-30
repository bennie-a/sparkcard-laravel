<DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>aaaa</title>
<!-- <meta name="description" itemprop="description" content="@yield('description')">
<meta name="keywords" itemprop="keywords" content="@yield('keywords')">
<link href="/css/star/layout.css" rel="stylesheet">
@yield('pageCss') -->
</head>
<body>
 
@yield('header')
 
<div class="contents">
    <!-- コンテンツ -->
    <div class="main">
        @yield('content')
    </div>
    <!-- 共通メニュー -->
    <div class="sub">
        @yield('submenu')
    </div>
</div>
 
@yield('footer')
</body>
</html>