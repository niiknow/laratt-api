<?php
namespace Api\Extra\Middlewares;

use Closure;
use Niiknow\Laratt\TenancyResolver;

class ValidApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tenant = TenancyResolver::resolve();
        if (!preg_match('/[a-z]{1}[0-9a-z_]{2,19}/', $tenant)) {
            return response()->json(['error' => 'You must provide a valid tenant id.'], 422);
        }

        $apiKey = config('admin.api_key');
        if ($apiKey) {
            $key = $request->header('x-api-key');
            if (!isset($key)) {
                $key = $request->query('x-api-key');
            }
            if ($key !== $apiKey) {
                return response()->json(['error' => 'Not authorized'], 403);
            }
        }

        return $next($request);
    }
}
