<?php

namespace App\Services;

use App\Services\BaseServices;
use App\Repositories\PraiseRepository;

class PraiseServices extends BaseServices
{
    private $praiseRepos;

    public function __construct(PraiseRepository $praiseRepos)
    {
        $this->praiseRepos = $praiseRepos;
    }

    public function createPost($params, $operationInfo)
    {

    }

    public function cancelPost($params, $operationInfo)
    {
        
    }

    public function createReply($params, $operationInfo)
    {
        
    }

    public function cancelReply($params, $operationInfo)
    {
        
    }
}
