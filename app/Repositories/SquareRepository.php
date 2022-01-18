<?php

namespace App\Repositories;
namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Square\SquareModel;
use App\Models\Square\SquareOpLogModel;
use App\Models\Square\SquareFollowModel;
use Carbon\Carbon;


class SquareRepository extends BaseRepository
{
    private $squareModel;
    private $squareOpLogModel;
    private $squareFollowModel;

    public function __construct(
        SquareModel $squareModel,
        SquareOpLogModel $squareOpLogModel,
        SquareFollowModel $squareFollowModel
    ) {
        $this->squareModel = $squareModel;
        $this->squareOpLogModel = $squareOpLogModel;
        $this->squareFollowModel = $squareFollowModel;
    }

    public function getList($params)
    {
        return $this->squareModel->getList($params);
    }

    public function detail($squareId)
    {
        return $this->squareModel->getById($squareId);
    }

    public function createSquare($params, $operationInfo)
    {
        return $this->commonCreate(
            $this->squareModel,
            $params,
            $this->squareOpLogModel,
            $operationInfo,
            '创建广场'
        );
    }

    public function updateSquare($params, $operationInfo)
    {
        $squareId = $params['square_id'] ?? 0;
        $this->commonUpdate(
            $squareId,
            $this->squareModel,
            $this->squareOpLogModel,
            $params,
            $operationInfo,
            '更新广场'
        );

        return $this->squareModel->getById($squareId);
    }

    public function setFollow($squareId, $userId)
    {
        return $this->squareFollowModel->insertGetId(
            [
                'square_id' => $squareId,
                'follower_id' => $userId,
                'created_at' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function cancelFollow($squareId, $userId)
    {
        return $this->squareFollowModel->updateByCondition([
            'square_id' => $squareId,
            'follower_id' => $userId,
            'is_del' => 0
        ], [
            'is_del' => 1,
            'deleted_at' => Carbon::now()->toDateTimeString()
        ]);
    }
}