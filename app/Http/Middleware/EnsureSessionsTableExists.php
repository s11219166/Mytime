<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class EnsureSessionsTableExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if using database sessions
        if (Config::get('session.driver') === 'database') {
            // Check if sessions table exists
            if (!Schema::hasTable('sessions')) {
                // Fall back to file sessions to prevent 419 errors
                Config::set('session.driver', 'file');
            }
        }

        return $next($request);
    }
}
