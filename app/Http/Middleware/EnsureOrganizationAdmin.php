<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_organization_admin) {
            return redirect()->route('member.home');
        }

        return $next($request);
    }
}
