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
        $this->renderable(fn (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) => sendResponse(null, 'No se encuentra autorizado', 403));

        /* Cuando no encuentra una ruta HTTP */
        $this->renderable(fn (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) => sendResponse(null, $e->getMessage(), 404));

        /* Método HTTP no permitido */
        $this->renderable(fn (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) => sendResponse(null, 'Método HTTP no permitido', 405));

        /* Cuando ocurre una error en la base de datos */
        $this->renderable(fn (\Illuminate\Database\QueryException $e, $request) => sendResponse(null, $e->getMessage(), 301));
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return sendResponse(null, $exception->errors(), $exception->status);
    }
}
