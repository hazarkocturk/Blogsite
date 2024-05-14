<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('UserMiddleware: Checking user authentication');

        if (!Auth::check()) {
            Log::warning('UserMiddleware: Unauthenticated user');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (Auth::user()->userType !== 'user') {
            Log::warning('UserMiddleware: Unauthorized user type', ['user_id' => Auth::id(), 'user_type' => Auth::user()->userType]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info('UserMiddleware: User authorized', ['user_id' => Auth::id()]);
        return $next($request);
    }
}
