<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*$middleware->api(append: [
                ApiLoggingMiddleware::class,
            ]);*/
        $middleware->alias([
            'role' => \App\Http\Middlewares\CheckRole::class,
        ]);

        $middleware->redirectGuestsTo(fn () => null);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Gestion des erreurs de validation
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return api_validation_error($e->errors(), 'Erreur de validation.');
            }
        });

        // Gestion des exceptions d'authentification
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            return api_unauthorized('Non authentifié.');
        });

        // Gestion des exceptions d'autorisation
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return api_forbidden($e->getMessage() ?: 'Action non autorisée.');
            }
        });

        // Gestion des erreurs 404
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return api_not_found('Ressource introuvable.');
            }
        });

        // Gestion des erreurs de throttle (trop de requêtes)
        $exceptions->renderable(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return api_error('Trop de tentatives. Veuillez réessayer plus tard.', [
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
                ], 429);
            }
        });

        // Gestion des erreurs de base de données
        $exceptions->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->expectsJson()) {
                $message = config('app.debug') ? $e->getMessage() : 'Erreur de base de données.';

                return api_error($message, null, 500);
            }
        });

        // Gestion des erreurs génériques
        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson() && ! ($e instanceof \Illuminate\Http\Exceptions\HttpResponseException)) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getCode() : (method_exists($e, 'getCode') && $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
                $message = config('app.debug') ? $e->getMessage() : 'Une erreur inattendue est survenue.';

                return api_error($message, [
                    'type' => get_class($e),
                    'trace' => config('app.debug') ? array_slice($e->getTrace(), 0, 5) : null,
                ], $statusCode);
            }
        });
    })->create();
