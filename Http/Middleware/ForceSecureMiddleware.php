<?php namespace Cms\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

class ForceSecureMiddleware
{
    /**
     * Force Secure on the CMS
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('cms.core.app.force-secure', 'false') === 'false') {
            return $next($request);
        }

        if (Request::secure() === false) {
            return redirect()->secure(Request::path());
        }

        return $response;
    }

}
