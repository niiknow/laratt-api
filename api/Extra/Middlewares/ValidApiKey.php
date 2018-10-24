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
        if (!preg_match('/[a-z]{1}[0-9a-z]{2,19}/', $tenant)) {
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
