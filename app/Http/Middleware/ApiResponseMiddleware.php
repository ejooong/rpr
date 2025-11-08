<?php
// app/Http/Middleware/ApiResponseMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only format JSON responses
        if ($request->expectsJson() || $request->is('api/*')) {
            $original = $response->original;

            // If it's already a formatted response, leave it as is
            if (is_array($original) && isset($original['success'])) {
                return $response;
            }

            // Format successful responses
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $formatted = [
                    'success' => true,
                    'data' => $original,
                    'message' => 'Request berhasil'
                ];
                return response()->json($formatted);
            }
        }

        return $response;
    }
}