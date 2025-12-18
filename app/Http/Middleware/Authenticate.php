<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Debug logging
            \Log::warning('Unauthenticated access attempt', [
                'url' => $request->fullUrl(),
                'session_id' => session()->getId(),
                'has_session' => $request->hasSession(),
                'cookies' => $request->cookies->keys()
            ]);
            
            return route('login');
        }
    }
}
