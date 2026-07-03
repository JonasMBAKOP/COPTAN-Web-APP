<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RecordUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            // Key expires after 5 minutes — considered "online" when present
            Cache::put('user-is-online-' . $request->user()->id, true, now()->addMinutes(5));
        }

        return $next($request);
    }
}
