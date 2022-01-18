<?php

namespace App\Services;

use App\Repositories\SquareRepository;

class SquareServices extends BaseServices
{

    private $squareRepos;

    public function __construct(SquareRepository $squareRepos)
    {
        $this->squareRepos = $squareRepos;
    }

    public function getList($params)
    {
        return $this->squareRepos->getList($params);
    }

    public function createSquare($params, $operationInfo)
    {
        return $this->squareRepos->createSquare($params, $operationInfo);
    }

    public function updateSquare($params, $operationInfo)
    {
        return $this->squareRepos->updateSquare($params, $operationInfo);
    }

    public function getDetail($squareId, $userId)
    {
        return $this->squareRepos->detail($squareId);
    }

    public function setFollow($squareId, $userId)
    {
        return $this->squareRepos->setFollow($squareId, $userId);
    }

    public function cancelFollow($squareId, $userId)
    {
        return $this->squareRepos->cancelFollow($squareId, $userId);
    }
}