<?php

namespace App\Services\Admin;

use App\Services\BaseServices;
use App\Repositories\SquareRepository;
use Carbon\Carbon;
use App\Libs\UtilLib;

class SquareServices extends BaseServices
{
    private $squareRepos;

    public function __construct(SquareRepository $squareRepos)
    {
        $this->squareRepos = $squareRepos;
    }

    /**
     * 广场列表
     * @param [type] $params
     * @return void
     */
    public function getList($params)
    {
        $res = $this->squareRepos->getList($params);
        $list = $res['list'] ?? [];
        if ($list) {
            foreach ($list as &$info) {
                $info['verify_status_display'] = UtilLib::getConfigByCode($info['verify_status'], 'display.square_verify_status', 'desc');
            }
            $res['list'] = $list;
        }

        return $res;
    }

    /**
     * 广场详情
     * @param [type] $params
     * @return void
     */
    public function detail($params)
    {
        $squareId = $params['square_id'] ?? 0;
        return $this->squareRepos->detail($squareId);
    }

    /**
     * 广场模糊搜索
     * @param [type] $params
     * @return void
     */
    public function suggest($params)
    {
        return $this->squareRepos->suggest($params);
    }

    /**
     * 广场审核通过
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function doApprove($params, $operationInfo)
    {
        $params['verify_status'] = config('display.square_verify_status.approved.code');
        return $this->squareRepos->updateSquare($params, $operationInfo, '管理员审核通过');
    }

    /**
     * 广场审核驳回
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function doReject($params, $operationInfo)
    {
        $params['verify_status'] = config('display.square_verify_status.rejected.code');
        return $this->squareRepos->updateSquare($params, $operationInfo, '管理员审核驳回');
    }

    /**
     * 更换广场主
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function doSwitch($params, $operationInfo)
    {
        $params['verify_status'] = config('display.square_verify_status.approved.code');
        return $this->squareRepos->updateSquare($params, $operationInfo, '管理员更换广场主');
    }

    /**
     * 更新广场
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function update($params, $operationInfo)
    {
        return $this->squareRepos->updateSquare($params, $operationInfo, '管理员更新广场信息');
    }

    /**
     * 删除广场
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function delete($params, $operationInfo)
    {
        $params['is_del'] = 1;
        $params['deleted_at'] = Carbon::now()->toDateTimeString();
        $params['verify_status'] = config('display.square_verify_status.dismissed.code');
        return $this->squareRepos->updateSquare($params, $operationInfo, '管理员解散广场', 'delete');
    }
}