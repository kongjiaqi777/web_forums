<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * build success return function
     */
    protected function buildSucceed($result, $succeedMessage = 'success')
    {
        if ($result) {
            return [
                'code' => 0,
                'msg'  => $succeedMessage,
                'info' => $result
            ];
        }
        return [
                'code' => 0,
                'msg'  => $succeedMessage,
        ];
    }

    /**
     * get operation parameters
     */
    protected function getOperationInfo($request)
    {
        $operationInfo = [
            'operator_id' => $request->input('operator_id', 0),
            // 'operator_name' => $request->input('operator_name', ''),
            'operator_type' => $request->input('operator_type', 0),
            'operator_ip' => $request->input('operator_ip', '123.123.123.123'),
        ];
        return $operationInfo;
    }
}
