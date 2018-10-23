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
        // validate api key
        return $next($request);
    }
}
