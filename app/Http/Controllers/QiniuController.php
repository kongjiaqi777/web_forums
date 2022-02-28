<?php

namespace App\Http\Controllers;

// require 'path_to_sdk/vendor/autoload.php';

use App\Exceptions\NoStackException;
use Qiniu\Auth;

class QiniuController extends Controller
{
    /**
     * @api {GET} /v1/common/get_qiniu_token 获取七牛token
     * @apiVersion 1.0.0
     * @apiName 获取七牛token
     * @apiGroup Common
     * @apiSuccessExample Success-Response
     * {
            "code": 0,
            "msg": "success",
            "info": {
                "qiniu_token": "FDokjgRc7O2psPsU0Tl7eVO8cLBV7sMWaSJ7v1u6:3VturKbWMT_4xOS3N9Vcs3li9G0=:eyJzY29wZSI6IkJ1Y2tldF9OYW1lIiwiZGVhZGxpbmUiOjE2NDYwMzkxNzl9"
            }
        }
     */
    public function getQiniuToken()
    {
        $accessKey = env('QINIUAK');
        $secretKey = env('QINIUSK');
        // 初始化签权对象
        if ($accessKey && $secretKey) {
            $auth = new Auth($accessKey, $secretKey);
            $bucket = 'biya';
            // 生成上传Token
            $token = $auth->uploadToken($bucket);
            return $this->buildSucceed([
                'qiniu_token' => $token
            ]);
        } else {
            throw New NoStackException('七牛账户没有配置');
        }
    }
}
