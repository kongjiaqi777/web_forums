<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Message\MessageTemplateModel;
use App\Models\Message\MessageModel;
use App\Models\Message\MessageOpLogModel;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\User\UserModel;
use App\Models\Post\PostModel;
use App\Models\Square\SquareModel;
use App\Models\Post\ReplyModel;


class MessageRepository extends BaseRepository
{
    private $messageModel;
    private $templateModel;
    private $messageOpLogModel;
    private $postModel;
    private $squareModel;
    private $userModel;
    private $replyModel;

    public function __construct(
        MessageTemplateModel $templateModel,
        MessageModel $messageModel,
        MessageOpLogModel $messageOpLogModel,
        PostModel $postModel,
        SquareModel $squareModel,
        ReplyModel $replyModel,
        UserModel $userModel
    ) {
        $this->templateModel = $templateModel;
        $this->messageModel = $messageModel;
        $this->messageOpLogModel = $messageOpLogModel;
        $this->postModel = $postModel;
        $this->squareModel = $squareModel;
        $this->replyModel = $replyModel;
        $this->userModel = $userModel;
    }

    public function sendMessage($msgType, $userId, $operationInfo, $replaceParams)
    {
        $messageTemplate = $this->templateModel->getFirstByCondition(['msg_type' => $msgType]);
        $templateId = $messageTemplate['id'] ?? 0;
        $messageTemplate = Arr::only($messageTemplate, [
            'msg_type',
            'msg_title',
            'msg_body'
        ]);
        
        $replaceParams ['msg_body'] = $messageTemplate ['msg_body'];
        $messageTemplate ['template_id'] = $templateId;
        $messageTemplate ['user_id'] = $userId;
        $messageTemplate ['msg_body'] = $this->getConfigParam($messageTemplate ['msg_body'], $replaceParams);

        return $this->commonCreate(
            $this->messageModel,
            $messageTemplate,
            $this->messageOpLogModel,
            $operationInfo
        );
    }

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

    public function detail($params)
    {
        $messageId = $params['message_id'] ?? 0;
        return $this->messageModel->getById($messageId);
    }

    public function getConfigParam($msgBody, $params)
    {
        $replaceTimes = substr_count($msgBody , '{{');
        $configs = $this->getConfig();

        for($i=1; $i<=$replaceTimes; $i++) {
            foreach($configs as $configKey => $config) {
                $search = strstr($msgBody, $configKey);
                if ($search) {
                    $funcName = $config['funcName'];
                    $replaceParams = Arr::only($params, $config['param']);
                    $msgBody = $this->$funcName($replaceParams);
                }
            }
        }
        return $msgBody;
    }

    public function getConfig()
    {
        return [
            '{{square_name}}' => [
                'funcName' => 'replaceSquareName',
                'param' => [
                    'square_id', 'msg_body'
                ],
            ],
            '{{user_name}}' => [
                'funcName' => 'replaceUserName',
                'param' => [
                    'user_id', 'msg_body'
                ],
            ],
            '{{title}}' => [
                'funcName' => 'replacePostTitle',
                'param' => [
                    'post_id', 'msg_body'
                ],
            ],
            '{{content}}' => [
                'funcName' => 'replaceReplyContent',
                'param' => [
                    'reply_id', 'msg_body'
                ],
            ],
        ];
    }

    public function replaceSquareName($params)
    {
        $squareId = $params['square_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $squareInfo = $this->squareModel->getById($squareId);
        $squareName = $squareInfo['name'] ?? '';
        return str_replace('{{square_name}}', '《'.$squareName.'》', $msgBody);
    }

    public function replaceUserName($params)
    {
        $userId = $params['user_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $userInfo = $this->userModel->getById($userId);
        $userName = $userInfo['nickname'] ?? '';
        return str_replace('{{user_name}}', '《'.$userName.'》', $msgBody);
    }

    public function replacePostTitle($params)
    {
        $postId = $params['post_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $postInfo = $this->postModel->getById($postId);
        $title = $postInfo['title'] ?? '';
        if (count($title) > 10) {
            $title = substr($title, 0, 10);
        }
        return str_replace('{{title}}', '《'.$title.'》', $msgBody);
    }

    public function replaceReplyContent($params)
    {
        $replyId = $params['reply_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $replyInfo = $this->replyModel->getById($replyId);
        $content = $replyInfo['content'] ?? '';
        if (count($content) > 10) {
            $content = substr($content, 0, 10);
        }
        return str_replace('{{content}}', '《'.$content.'》', $msgBody);
    }
}