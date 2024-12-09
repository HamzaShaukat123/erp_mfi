<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure the user is authenticated
        // if (!Auth::check()) {
        //     return redirect('/login'); // or wherever you want to redirect unauthenticated users
        // }

        if (session()->has('lastActivity')) {
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            if (time() - session('lastActivity') > $timeout) {
                session()->flush(); // Clear the session
                return redirect('/login'); // Redirect to login or any page
            }
        }

        session(['lastActivity' => time()]); // Update last activity time
        return $next($request);
    }
}
