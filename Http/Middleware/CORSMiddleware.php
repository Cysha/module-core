<?php namespace Cms\Modules\Core\Http\Middleware;

use Closure;

class CORSMiddleware
{
    /**
     * Minify HTML
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('cms.core.app.cors', 'false') === 'false') {
            return $next($request);
        }

        $response = $next($request);
        if(!($response instanceof \Illuminate\Http\Response)) {
            $response
                ->header('Access-Control-Allow-Origin' , $request->server('HTTP_HOST'))
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Origin')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }

}
