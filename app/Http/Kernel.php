<?php


protected $routeMiddleware = [
    // ... middleware الأخرى
    'admin.auth' => \App\Http\Middleware\AdminAuth::class,
    'admin.guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
];