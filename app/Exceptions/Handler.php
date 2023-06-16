<?php

namespace App\Exceptions;

use HttpRequestException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
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

//    /**
//     * Register the exception handling callbacks for the application.
//     *
//     * @return void
//     */
//    public function register()
//    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });
//    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof MissingAbilityException){
            return response()->json([
                "errors"=>[
                    'status'=>401,
                    'message'=>'Unauthenticated'
                ]
            ],401);
        }
        $ex = $this->prepareException($e);
        if ($ex instanceof HttpRequestException){
            return $ex->getResponse();
        }elseif ($ex instanceof AuthenticationException){
            return $this->unauthenticated($request,$ex);
        }elseif ($ex instanceof ValidationException){
            return $this->convertExceptionToResponse($ex);
        }
        return $this->prepareResponse($request,$ex);
    }

    public function unauthentticated(){
        return response()->json(["errors"=>[
            'status'=>401,
            'message'=>'Unauthenticated'
        ]
        ],401);
    }
}
