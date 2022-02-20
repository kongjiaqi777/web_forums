<?php

namespace App\Services\Admin;

use App\Repositories\PostRepository;
use App\Repositories\ReplyRepository;

class PostServices
{
    private $postRepos;
    private $replyRepos;

    public function __construct(
        PostRepository $postRepos,
        ReplyRepository $replyRepos
    ) {
        $this->postRepos = $postRepos;
        $this->replyRepos = $replyRepos;
    }

    /**
     * 列表
     * @param [type] $params
     * @return void
     */
    public function getList($params)
    {
        return $this->postRepos->getList($params);
    }

    /**
     * 详情
     * @param [type] $params
     * @return void
     */
    public function detail($params)
    {
        return $this->postRepos->detailPost($params);
    }

    /**
     * 设置置顶
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function setTop($params, $operationInfo)
    {
        return $this->postRepos->adminSetTop($params, $operationInfo);
    }

    /**
     * 删除
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function delete($params, $operationInfo)
    {
        $msgType = config('display.msg_type.admin_delete_post.code');
        return $this->postRepos->delete($params, $operationInfo, $msgType);
    }

    /**
     * 删除评论
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function deleteReply($params, $operationInfo)
    {
        return $this->replyRepos->delete($params, $operationInfo);
    }

    /**
     * 模糊搜索标题
     * @param [type] $params
     * @return void
     */
    public function suggest($params)
    {
        return $this->postRepos->suggest($params);
    }

}
