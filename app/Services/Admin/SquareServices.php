<?php

namespace App\Services\Admin;

use App\Exceptions\NoStackException;
use App\Services\BaseServices;
use App\Repositories\SquareRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use App\Libs\UtilLib;

class SquareServices extends BaseServices
{
    private $squareRepos;
    private $userRepos;

    public function __construct(
        SquareRepository $squareRepos,
        UserRepository $userRepos
    ) {
        $this->squareRepos = $squareRepos;
        $this->userRepos = $userRepos;
    }

    /**
     * 广场列表
     * @param [type] $params
     * @return void
     */
    public function getList($params)
    {
        $res = $this->squareRepos->getList($params, false, 0, true);
        $list = $res['list'] ?? [];
        if ($list) {
            foreach ($list as &$info) {
                $info['verify_status_display'] = UtilLib::getConfigByCode(
                    $info['verify_status'],
                    'display.square_verify_status',
                    'desc'
                );
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
        $detail = $this->squareRepos->detail($squareId);
        if ($detail) {
            $detail['verify_status_display'] = UtilLib::getConfigByCode(
                $detail['verify_status'],
                'display.square_verify_status',
                'desc'
            );
        }
        return $detail;
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
        
        // validate for creater id
        $createrId = $params['creater_id'] ?? 0;
        $createrInfo = $this->userRepos->getById($createrId);
        if (empty($createrInfo)) {
            throw New NoStackException('广场主信息不存在，请重新选择');
        }

        // validate for square verify status
        $squareId = $params['square_id'] ?? 0;
        $squareInfo = $this->squareRepos->detail($squareId);

        if ($squareInfo != config('display.square_verify_status.dismissed.code')) {
            throw New NoStackException('广场状态不合理，无法操作');
        }
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