<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticatedCustomerCheckout
{
    public function handle(Request $request, Closure $next)
    {
        $key = (string) config('services.marketplace.checkout_key');
        abort_if($key === '', 503, 'Le service de commande client doit être configuré.');
        abort_unless(hash_equals($key, (string) $request->header('X-Marketplace-Key'))
            && preg_match('/^[1-9][0-9]*$/', (string) $request->header('X-Customer-Id')), 401, 'Une connexion client est obligatoire.');
        return $next($request);
    }
}
