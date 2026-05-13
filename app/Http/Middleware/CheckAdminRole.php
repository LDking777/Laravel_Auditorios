<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->user()?->role, ['admin', 'staff'])) {
            return redirect()->route('spaces.public.index')
                ->with('error', 'No tienes permisos de administrativo.');
        }

        return $next($request);
    }
}
