<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class JwtMiddleware extends \Tymon\JWTAuth\Http\Middleware\BaseMiddleware
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
        try {
            $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return sendResponse(null, 'Token invalido', 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return sendResponse(null, 'Token expiro', 401);
            } else {
                return sendResponse(null, 'Token no encontrado', 401);
            }
        }
        return $next($request);
    }
}
