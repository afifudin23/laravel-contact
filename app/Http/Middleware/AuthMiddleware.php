<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $access_token = $request->bearerToken();
        if (!$access_token) return response()->json(['errors' => ['message' => 'Unauthorized: Token missing']], 401);

        $user = User::where('access_token', $access_token)->first();

        if (!$user) return response()->json(['errors' => ['message' => 'Unauthorized: Invalid token']], 401);


        Auth::setUser($user);
        return $next($request);
    }
}
