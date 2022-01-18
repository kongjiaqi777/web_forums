<?php

namespace App\Repositories;

use App\Exceptions\NoStackException;
use App\Repositories\BaseRepository;
use App\Models\Post\PostModel;
use App\Models\Post\PostOpLogModel;
use App\Models\Square\SquareModel;

class PostRepository extends BaseRepository
{
    private $postModel;
    private $postOpLogModel;
    private $squareModel;

    public function __construct(
        PostModel $postModel,
        PostOpLogModel $postOpLogModel,
        SquareModel $squareModel
    ) {
        $this->postModel = $postModel;
        $this->postOpLogModel = $postOpLogModel;
        $this->squareModel = $squareModel;
    }

    public function getList($params)
    {
        return $this->postModel->getList($params);
    }

    public function createPost($params, $operationInfo)
    {
        $squareId = $params['square_id'] ?? 0;
        $squareInfo = $this->squareModel->getById($squareId);

        if (empty($squareInfo)) {
            throw New NoStackException('广场信息有误，无法创建');
        }

        return $this->commonCreate(
            $this->postModel,
            $params,
            $this->postOpLogModel,
            $operationInfo,
            '创建广播'
        );
    }

    public function detailPost($params)
    {
        $postId = $params['post_id'] ?? 0;
        return $this->postModel->getById($postId);
    }
}