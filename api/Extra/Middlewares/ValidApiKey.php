<?php
namespace Api\Extra\Middlewares;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class ValidApiKey
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tenant = tenantId();
        $len    = strlen($tenant);
        if ($len < 3 || $len > 20) {
            return response()->json(['error' => 'You must provide a valid tenant id.'], 422);
        }

        $apiKey = config('admin.token');
        if ($apiKey) {
            $key = $request->header('x-token');
            if ($key != $apiKey) {
                return response()->json(['error' => 'Not authorized.'], 403);
            }
        }

        return $next($request);
    }
}
