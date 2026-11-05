<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MarketplaceAdmin
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->account_type === 'admin', 403);

        return $next($request);
    }
}
