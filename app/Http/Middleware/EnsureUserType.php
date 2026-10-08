<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        if (
            $request->session()->get('user_type') !== $type
            || ! $request->session()->has('user_id')
        ) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
