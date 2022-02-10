<?php

namespace App\Libs;

use App\Exceptions\NoStackException;
use Illuminate\Support\Arr;
use App\Models\Message\MessageTemplateModel;
use App\Models\Message\MessageModel;
// use App\Models\Message\MessageOpLogModel;
use App\Models\User\UserModel;
use App\Models\Post\PostModel;
use App\Models\Square\SquareModel;
use App\Models\Post\ReplyModel;


class MessageLib
{
    // 发消息没有oplog
    public static function sendMessage($msgType, $userIds, $replaceParams)
    {
        if (empty($msgType) || empty($userIds)) {
            throw New NoStackException('参数错误');
        }

        $templateModel = new MessageTemplateModel();
        $messageTemplate = $templateModel->getFirstByCondition(['msg_type' => $msgType]);
        $templateId = $messageTemplate['id'] ?? 0;
        $messageTemplate = Arr::only($messageTemplate, [
            'msg_type',
            'msg_title',
            'msg_body'
        ]);
        
        $replaceParams ['msg_body'] = $messageTemplate ['msg_body'];
        $messageTemplate ['template_id'] = $templateId;
        $messageTemplate ['msg_body'] = self::getConfigParam($messageTemplate ['msg_body'], $replaceParams);

        // 扩展多个userId
        $inserts = self::getInsertArray($userIds, $messageTemplate);

        // insert
        $messageModel = new MessageModel();
        return $messageModel->multiInsert($inserts);
    }

    public static function getInsertArray($userIds, $messageTemplate)
    {
        $multiInsert = [];

        foreach ($userIds as $userId) {
            $messageTemplate['user_id'] = $userId;
            $multiInsert[] = $messageTemplate;
        }

        return $multiInsert;
    }

    public static function getConfigParam($msgBody, $params)
    {
        $replaceTimes = substr_count($msgBody , '{{');
        $configs =  [
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

        for($i=1; $i<=$replaceTimes; $i++) {
            foreach($configs as $configKey => $config) {
                $search = strstr($msgBody, $configKey);
                if ($search) {
                    $funcName = $config['funcName'];
                    $replaceParams = Arr::only($params, $config['param']);
                    $msgBody = self::$funcName($replaceParams);
                }
            }
        }
        return $msgBody;
    }

    public static function replaceSquareName($params)
    {
        $squareModel = new SquareModel();
        $squareId = $params['square_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $squareInfo = $squareModel->getById($squareId);
        $squareName = $squareInfo['name'] ?? '';
        return str_replace('{{square_name}}', '《'.$squareName.'》', $msgBody);
    }

    public static function replaceUserName($params)
    {
        $userModel = new UserModel();
        $userId = $params['user_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $userInfo = $userModel->getById($userId);
        $userName = $userInfo['nickname'] ?? '';
        return str_replace('{{user_name}}', '《'.$userName.'》', $msgBody);
    }

    public static function replacePostTitle($params)
    {
        $postModel = new PostModel();
        $postId = $params['post_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $postInfo = $postModel->getById($postId);
        $title = $postInfo['title'] ?? '';
        if (count($title) > 10) {
            $title = substr($title, 0, 10);
        }
        return str_replace('{{title}}', '《'.$title.'》', $msgBody);
    }

    public static function replaceReplyContent($params)
    {
        $replyModel = new ReplyModel();
        $replyId = $params['reply_id'] ?? 0;
        $msgBody = $params['msg_body'] ?? '';
        $replyInfo = $replyModel->getById($replyId);
        $content = $replyInfo['content'] ?? '';
        if (count($content) > 10) {
            $content = substr($content, 0, 10);
        }
        return str_replace('{{content}}', '《'.$content.'》', $msgBody);
    }
}