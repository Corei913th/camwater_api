<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MonitorResponseTime
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        // On stocke les durées dans le cache pour calculer une moyenne glissante
        // On garde les 100 dernières durées par exemple
        $durations = Cache::get('app_request_durations', []);
        $durations[] = $duration;
        
        if (count($durations) > 100) {
            array_shift($durations);
        }

        Cache::forever('app_request_durations', $durations);

        return $response;
    }
}
