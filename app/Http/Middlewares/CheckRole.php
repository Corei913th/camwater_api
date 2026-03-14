<?php

namespace App\Http\Middlewares;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return api_error('Non authentifié', 'UNAUTHENTICATED', 401);
        }

        $user = $request->user();


        /*if ($user->role === Role::ADMIN) {
            return $next($request);
        }


        $userTypeValue = $user->role instanceof Role
            ? $user->role->value
            : $user->role;

        if (in_array(Role::ADMIN->value, Role::values())) {
            return $next($request);
        }

        if (in_array($userTypeValue, $roles)) {
            return $next($request);
        }*/



        $hasRole = !empty(array_intersect($roles, [$user->role->value]));

        if (!$hasRole) {
            return api_error(
                'Accès refusé. Rôle requis: ' . implode(' ou ', $roles),
                'FORBIDDEN',
                403
            );
        }

        return $next($request);
    }
}
