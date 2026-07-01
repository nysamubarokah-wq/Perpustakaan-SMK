<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
    
    $middleware->redirectGuestsTo('/login');
    
    $middleware->redirectUsersTo(function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return route('admin.dashboard');
    }
    return route('dashboard');
});
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
