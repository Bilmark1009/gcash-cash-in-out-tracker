<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOnboarded
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->onboarded) {
            // Don't redirect if already on the onboarding page or if it's a login/logout route
            $currentPath = $request->path();
            
            if (!str_contains($currentPath, 'onboarding') && 
                !str_contains($currentPath, 'login') && 
                !str_contains($currentPath, 'logout')) {
                return redirect()->route('filament.admin.pages.onboarding');
            }
        }

        return $next($request);
    }
}
