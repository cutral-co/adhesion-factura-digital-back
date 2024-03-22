<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        /* Cuando el usuario no se encuentra autorizado */
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if (app()->environment('production')) {
                return sendResponse(null, 'No se encuentra autorizado', 401);
            } else {
                return sendResponse(null, $e->getMessage(), 401);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {

            if (app()->environment('production')) {
                return sendResponse(null, 'Acceso denegado', 302);
            } else {
                return sendResponse(null, $e->getMessage(), 302);
            }
        });
        /* Cuando no encuentra una ruta HTTP */
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if (app()->environment('production')) {
                return sendResponse(null, 'No existe el recursos', 404);
            } else {
                return sendResponse(null, $e->getMessage(), 404);
            }
        });

        /* Método HTTP no permitido */
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            return sendResponse(null, 'Método HTTP no permitido', 405);
        });

        /* Problema con la base de datos */
        $this->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if (app()->environment('production')) {
                return sendResponse(null, 'Hubo un problema con la base de datos', 301);
            } else {
                return sendResponse(null, $e->getMessage(), 301);
            }
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return sendResponse(null, $exception->errors(), $exception->status);
    }
}
