<?php

namespace App\Http\Middleware;

use App\Models\Assemblee;
use App\Models\Budget;
use App\Scopes\TenantScope;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class ApplyTenantScopes
{
    public function handle(Request $request, Closure $next): Response
    {
        Assemblee::addGlobalScope(new TenantScope);
        Budget::addGlobalScope(new TenantScope);

        return $next($request);
    }
}
