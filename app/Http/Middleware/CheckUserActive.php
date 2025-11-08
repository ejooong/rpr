<?php
// app/Http/Middleware/CheckUserActive.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->aktif) {
            auth()->logout();
            return redirect('/login')->with('error', 'Akun Anda tidak aktif.');
        }

        return $next($request);
    }
}