<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
use Illuminate\Http\JsonResponse;
use Exception;

class FormatJsonOutput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $allowOrigin = [
            'http://localhost:9020',
            'http://localhost:8080',
            'http://localhost:9528',
            'http://113.31.126.66:9025',
            'http://36.255.221.163:8080',
        ];

        if (in_array($origin, $allowOrigin)) {
            header('Access-Control-Allow-Origin:'.$origin);
                header('Access-Control-Allow-Headers:Origin,Content-Type,Cookie,Accept,Authorization,X-Requested-With,token,X-XSRF-TOKEN,X-CSRF-TOKEN,HTTP_X_REQUEST_ID');
                header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, OPTIONS');
                header('Access-Control-Allow-Credentials:true');
        }

        $response = $next($request);
    
        //Render results generated by /app/Exceptions/Handler will still pass through this function.
        //It is processed by Exception Handler.
        if($response instanceof JsonResponse){
            return $response;
        }

        //For HttpException, not format as Json.
        //It is processed by Exception Handler.
        if($response->exception instanceof Exception){
            return $response;
        }

        $originalContent = $response->getOriginalContent();

        Log::info(sprintf("url %s params %s response %s", $request->url(), json_encode($request->all()), json_encode($originalContent)));

        if ($originalContent) {
            return response()->json([
                'code' => 0,
                'msg' => 'success',
                'info' => $originalContent
                ]);
        } else {
            return response()->json([
                'code' => 0,
                'msg' => 'success'
            ]);
        }
    }
}
