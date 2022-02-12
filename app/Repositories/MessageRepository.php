<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Message\MessageModel;
use App\Models\Message\MessageOpLogModel;
use Carbon\Carbon;


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

}