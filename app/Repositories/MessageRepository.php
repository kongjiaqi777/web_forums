<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Message\MessageModel;
use App\Models\Message\MessageOpLogModel;
use Carbon\Carbon;
use DB;


class MessageRepository extends BaseRepository
{
    private $messageModel;
    private $messageOpLogModel;

    public function __construct(
        MessageModel $messageModel,
        MessageOpLogModel $messageOpLogModel
    ) {
        $this->messageModel = $messageModel;
        $this->messageOpLogModel = $messageOpLogModel;
    }

    /**
     * 我收到的消息列表
     * @param [type] $params
     * @return void
     */
    public function myMessageList($params)
    {
        $page = $params['page'] ?? 1;
        $perpage = $params['perpage'] ?? 20;
        $params['is_del'] = 0;

        return $this->getDataList(
            $this->messageModel,
            ['*'],
            $params,
            $page,
            $perpage,
            null,
            ['created_at' => 'desc']
        );
    }

    /**
     * 标记消息已读
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function read($params, $operationInfo)
    {
        $messageId = $params['message_id'] ?? 0;
        return $this->commonUpdate(
            $messageId,
            $this->messageModel,
            $this->messageOpLogModel,
            [
                'is_read' => 1,
                'updated_at' => Carbon::now()->toDateTimeString()
            ],
            $operationInfo
        );
    }

    /**
     * 获取消息详情
     * @param [type] $params
     * @return void
     */
    public function detail($params)
    {
        $messageId = $params['message_id'] ?? 0;
        return $this->messageModel->getById($messageId);
    }

    public function delete($operationInfo)
    {
        $operatorId = $operationInfo['operator_id'] ?? 0;

        $deleteIds = $this->messageModel->getAll([
            'user_id' => $operatorId,
            'is_del' => 0
        ], [
            'id' => 'desc'
        ], [
            'id'
        ]);
       
        $deleteIds = array_column($deleteIds, 'id');

        if (empty($deleteIds)) {
            throw New NoStackException('无消息');
        }

        return DB::transaction(function () use ($deleteIds, $operationInfo) {
            try {
                $res = $this->messageModel
                ->whereIn('id', $deleteIds)
                ->update([
                    'is_del' => 1,
                    'deleted_at' => Carbon::now()->toDateTimeString()
                ]);
                $this->messageOpLogModel->saveDeleteOpLogDatas($deleteIds, $operationInfo, '用户清空消息');
                return $res;
            } catch (\Exception $e) {
                Log::info(sprintf('清空信息失败[Code][%s][Msg][%s][OperationInfo][%s]', $e->getCode(), $e->getMessage(), json_encode($operationInfo)));
                throw new NoStackException('清空信息失败');
            }
        });
    }

}