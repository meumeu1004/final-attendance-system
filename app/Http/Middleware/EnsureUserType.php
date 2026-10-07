<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $current = $request->session()->get('user_type');

        if (!$current) {
            return redirect()->route('login');
        }

        if ($current !== $type) {
            return redirect()->route(
                $current === 'admin' ? 'professor.dashboard' : 'student.dashboard'
            );
        }

        return $next($request);
    }
}