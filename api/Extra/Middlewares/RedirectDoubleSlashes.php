<?php
namespace Api\Extra\Middlewares;

use Closure;
use Illuminate\Support\Facades\Redirect;

class RedirectDoubleSlashes
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
        $str      = $request->getRequestUri();
        $replaced = preg_replace('#/+#', '/', $str);
        if ($str !== $replaced) {
            return Redirect::to($replaced, 301);
        }

        return $next($request);
    }
}
