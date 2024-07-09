<?php

namespace App\Http\Middleware;

use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

use App\Models\User;

class JwtMiddleware extends \PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        try {
            $token = JWTAuth::parseToken();

            $payload = $token->getPayload();

            $id = $payload('id');
            $cuit = $payload('cuit');

            !User::find($id) && User::create(['id' => $id, 'cuit' => $cuit]);

            JWTAuth::authenticate($token);
        } catch (\Exception $e) {
            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException) {
                return sendResponse(null, 'Llave invalida', 450);
            }
            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException) {
                return sendResponse(null, 'Llave expiro', 450);
            }

            return sendResponse($e, 'Llave sin autorización', 450);
        }

        return $next($request);
    }
}
