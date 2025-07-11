<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle AuthorizationException (from $this->authorize())
        $exceptions->render(function (AuthorizationException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'This action is unauthorized.',
                'success' => false
            ], 403);
        });

        // Handle AccessDeniedHttpException
        $exceptions->render(function (AccessDeniedHttpException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'This action is unauthorized.',
                'success' => false
            ], 403);
        });
    })->create();
