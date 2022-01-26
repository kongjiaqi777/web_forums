<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Log;
use Exception;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    public function register()
    {
        // NotFoundHttpException提示格式
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'code' => 404,
                'msg' => '您访问的地址不存在。'
            ]);
        });

        // MethodNotAllowedHttpException 提示格式
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'code' => 405,
                'msg' => '请求方法错误(GET or POST)。'
            ]);
        });

        // ValidationException提示格式
        $this->reportable(function (ValidationException $e, $request) {
            $this->logException($request, $e, 'debug');

            $allMessage = [];
            $responseData = $e->errors();
            // json_decode($e->errors(), true);

            if ($responseData) {
                foreach ($responseData as $item) {
                    array_push($allMessage, implode(',', $item));
                };
            } else {
                $responseData = 'validation failed';
            }
            return response()->json([
                'code' => $e->getCode() ? $e->getCode() : -1,
                'msg' => implode(';', $allMessage),
                'info' => $responseData,
            ]);
        });

        // NoStackException提示格式
        $this->renderable(function (NoStackException $e, $request) {
            $this->logException($request, $e, 'warning');

            $code = $e->getCode();
            $msg = $e->getMessage();

            return response()->json([
                'code' => $code,
                'msg' => $msg,
            ]);
        });
        
        // BaseException提示格式
        $this->renderable(function (BaseException $e, $request) {
            $this->logException($request, $e, $e->getLogLevel());
            $code = $e->getCode();
            $msg = $e->getMessage();

            return response()->json([
                'code' => $code,
                'msg' => $msg,
            ]);
        });

        // 通用错误提示格式
        $this->renderable(function (Throwable $e, $request) {
            $err = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'url' => $request->url(),
                'input' => $request->all(),
                /* 'strace' => $e->getTrace(), */
            ];
    
            $response = [
                'code' => $e->getCode(),
                'msg' => $err['message'],
                'error_info' => $err,
            ];
            return response()->json($response);    
        });
    }

    private function logException($request, Exception $e, $errorLevel = 'error')
    {
        $err = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
            'url' => $request->url(),
            'input' => $request->all(),
        ];

        switch ($errorLevel) {
            case 'error':
                Log::error($err);
                break;
            case 'warning':
                Log::warning($err);
                break;
            case 'debug':
                Log::debug($err);
                break;
            default:
                Log::info($err);
        }
    }
}
