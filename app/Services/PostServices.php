<?php

namespace App\Services;

use App\Repositories\PostRepository;

class PostServices
{
    private $postRepos;

    public function __construct(
        PostRepository $postRepos
    ) {
        $this->postRepos = $postRepos;
    }

    /**
     * 广播列表
     * @param [type] $params
     * @return void
     */
    public function getList($params, $isShowPraise, $operatorId)
    {
        $params['is_del'] = 0;
        return $this->postRepos->getList($params, $isShowPraise, $operatorId);
    }

    /**
     * 创建广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function createPost($params, $operationInfo)
    {
        return $this->postRepos->createPost($params, $operationInfo);
    }

    /**
     * 广播详情
     * @param [type] $params
     * @return void
     */
    public function detailPost($params, $joinPraiseFlag, $operatorId)
    {
        return $this->postRepos->detailPost($params, $joinPraiseFlag, $operatorId);
    }

    /**
     * 广播信息模糊搜索
     * @param [type] $params
     * @return void
     */
    public function suggest($params, $isShowPraise, $operatorId)
    {
        return $this->postRepos->suggest($params, $isShowPraise, $operatorId);
    }

    /**
     * 更新广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function updatePost($params, $operationInfo)
    {
        return $this->postRepos->updatePost($params, $operationInfo);
    }

    /**
     * 广播列表-无分页
     * @param [type] $params
     * @return void
     */
    public function getAll($params)
    {
        return $this->postRepos->getAll($params, [
            'top_rule' => 'desc',
            'created_at' => 'desc'
        ]);
    }

    /**
     * 设置广播置顶
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function setTop($params, $operationInfo)
    {
        return $this->postRepos->setTop($params, $operationInfo);
    }

    /**
     * 取消广播置顶
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function cancelTop($postId, $operationInfo)
    {
        return $this->postRepos->cancelTop($postId, $operationInfo);
    }

    /**
     * 删除广播
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function delete($params, $operationInfo)
    {
        $msgType = config('display.msg_type.owner_delete_post.code');
        return $this->postRepos->delete($params, $operationInfo, $msgType);
    }

    /**
     * 浏览列表
     * @param [type] $params
     * @return void
     */
    public function browseList($params,$operatorId)
    {
        return $this->postRepos->browseList($params, $operatorId);
    }

    /**
     * 添加广播浏览记录
     * @param [type] $params
     * @param [type] $operationInfo
     * @return void
     */
    public function addBrowseRecord($params, $operationInfo)
    {
        return $this->postRepos->addBrowseRecord($params, $operationInfo);
    }
}
