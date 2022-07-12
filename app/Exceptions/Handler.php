<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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

    public function render($request, Throwable $e)
    {
        $e = $this->prepareException($e);
        if($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif($e instanceof ValidationException) {
           return $this->convertValidationExceptionToResponse($e, $request);
        } 
        else{
            return response()->json([
                'message' => 'Ошибка сервера!',
                'errors' => $e->getMessage()
            ], 500);
        }
        
        return $this->prepareResponse($e, $request);
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
