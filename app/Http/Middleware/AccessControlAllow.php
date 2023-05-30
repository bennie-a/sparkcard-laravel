<?php

namespace App\Http\Middleware;

use Closure;

class AccessControlAllow
{
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://localhost:8082') // 「8080」は「Vueの開発用サーバー」に合わせましょう。
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}

