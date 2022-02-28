<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;

class CorsWebMiddleware
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
		 header('Access-Control-Allow-Origin:http://localhost:8080');
        header('Access-Control-Allow-Headers:X-Requested-With,Content-Type,token');
        header('Access-Control-Allow-Methods:GET, POST, PATCH, PUT, OPTIONS');
        header('Access-Control-Allow-Credentials:true');

	return next($request);

	$response = $next($request);    
	       
	$origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
	Log::info('Origin'.$request->header('origin'));
	Log::info(sprintf('RequestLog:[Origin Address][%s][method][%s][Time][%s][RequestParam][%s]',$origin, $request->method(), Carbon::now()->toDateTimeString(),json_encode($request->all())));
	$allow_origin = [
		'http://localhost:9020',
		'http://localhost:8080',
		'http://localhost:9528',
		
        ];
        // if (in_array($origin, $allow_origin)) {
        //    $response->header('Access-Control-Allow-Origin',$origin);
        //    $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept,Authorization,X-Requested-With,token,X-XSRF-TOKEN,X-CSRF-TOKEN');
        //    $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
        //    $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        //    $response->header('Access-Control-Allow-Credentials', 'true');
	//}
	 $headers = [
            'Access-Control-Allow-Origin' => 'http://localhost:8080',
            'Access-Control-Allow-Headers' => 'Origin,Content-Type,X-Requested-With,token,Cookie,X-CSRF-TOKEN,Accept,Authorization,X-XSRF-TOKEN',
            //'Access-Control-Expose-Headers' => 'Authorization,authenticated',
            'Access-Control-Allow-Methods' => 'GET,POST,PATCH,PUT,OPTIONS',
	    'Access-Control-Allow-Credentials' => 'true',
	    'Content-Type' => 'application/x-www-form-urlencode,multipart/form-data,text/plain'
        ];

	$IlluminateResponse = 'Illuminate\Http\Response';
        $SymfonyResopnse = 'Symfony\Component\HttpFoundation\Response';
        // 因为 response 可能是两个不同的类 设置header 方式不一样
        if ($response instanceof $IlluminateResponse) {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }
            return $response;
        }

        if ($response instanceof $SymfonyResopnse) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        }
        return $response;
    }
}
