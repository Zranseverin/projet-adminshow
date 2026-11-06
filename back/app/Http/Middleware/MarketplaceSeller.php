<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MarketplaceSeller
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->account_type === 'seller' && $request->user()->seller?->is_active, 403, 'Votre boutique est suspendue. Contactez la plateforme.');

        if (! $request->user()->hasVerifiedEmail()) return redirect()->route('verification.notice');
        return $next($request);
    }
}
