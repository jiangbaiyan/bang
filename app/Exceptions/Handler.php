<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Auth;
use src\Logger\Logger;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use src\ApiHelper\ApiResponse;
use src\Exceptions\UnAuthorizedException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return string|\Symfony\Component\HttpFoundation\Response
     * @throws UnAuthorizedException
     */
    public function render($request, Exception $exception)
    {
        $errArr = [
            'status' => $exception->getCode(),
            'msg' => $exception->getMessage(),
            'fileName' => $exception->getFile(),
            'line' => $exception->getLine(),
            'url' => $request->fullUrl(),
            'params' => $request->all(),
            'ip' => $request->ip(),
        ];
        if (!empty($exception->getMessage())){
            Logger::fatal(json_encode($errArr));
        }else{
            exit;
        }
        return ApiResponse::response($exception->getCode(),$exception->getMessage());
    }
}
