<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * 要排除 CSRF 验证的 URI。
     */
    protected $except = [
        // 👇 在这里添加你要排除的路由
        '/payment',
    ];
}
