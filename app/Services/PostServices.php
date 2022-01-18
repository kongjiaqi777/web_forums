<?php

namespace App\Services;

use App\Services\BaseServices;
use App\Repositories\PostRepository;

class PostServices extends BaseServices
{
    private $postRepos;

    public function __construct(
        PostRepository $postRepos
    ) {
        $this->postRepos = $postRepos;
    }

    public function getList($params)
    {
        return $this->postRepos->getList($params);
    }

    public function createPost($params, $operationInfo)
    {
        return $this->postRepos->createPost($params, $operationInfo);
    }

    public function detailPost($params)
    {
        return $this->postRepos->detailPost($params);
    }
}
