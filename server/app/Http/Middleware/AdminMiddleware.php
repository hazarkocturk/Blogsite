<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
        Log::info('AdminMiddleware: Checking user authentication');

        if (!Auth::check()) {
            Log::warning('AdminMiddleware: Unauthenticated user');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (Auth::user()->userType !== 'admin') {
            Log::warning('AdminMiddleware: Unauthorized user type', ['user_id' => Auth::id(), 'user_type' => Auth::user()->userType]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info('AdminMiddleware: User authorized', ['user_id' => Auth::id()]);
        return $next($request);
    }
}
