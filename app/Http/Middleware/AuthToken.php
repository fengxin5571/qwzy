<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Routing\Helpers;
class AuthToken
{
    use Helpers;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $supllier=auth('api')->user();
        if(empty($supllier)){
            return $this->response->error('token过期或不正确',401);
        }
        return $next($request);
    }
}
