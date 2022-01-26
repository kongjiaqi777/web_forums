<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Support\Arr;


class BaseOpLogModel extends BaseModel
{
    // op log对应的Id字段名，如square_id
    protected $idKey = 'id';
    protected $opConfigKey = 'common.operation_type';

    public function saveCreateOpLogDatas($datas, $operationInfo, $comment = '')
    {
        $logs = $this->getCreateOpLogDatas($datas, $operationInfo, $comment);
        return $this->insert($logs);
    }

    public function getCreateOpLogDatas($datas, $operationInfo, $comment = '')
    {
        $logs = [];
        if ($datas && $operationInfo) {
            $before = [];
            $opConfigKey = $this->opConfigKey;
            foreach ($datas as $id => $info) {
                $logs[] = array_merge([
                    'operation_type' => config($opConfigKey . '.create.code'),
                    $this->idKey => $id,
                    'before_change' => json_encode($before),
                    'after_change' => json_encode($info),
                    'comment' => $comment,
                    'created_at' => Carbon::now()->toDateTimeString()
                ], $operationInfo);
            }
        }
        return $logs;
    }

    public function saveUpdateOpLogDatas($news, $originals, $operationInfo, $comment = '', $operationTypeSpec = 'update')
    {
        $logs = $this->getUpdateOpLogDatas($news, $originals, $operationInfo, $comment, $operationTypeSpec);
        return $this->insert($logs);
    }

    public function getUpdateOpLogDatas($news, $originals, $operationInfo, $comment, $operationTypeSpec)
    {
        $logs = [];
        if ($news && $originals && $operationInfo) {
            $operationType = config($this->opConfigKey . '.'.$operationTypeSpec.'.code');

            foreach ($news as $id => $info) {
                $before = Arr::get($originals, $id, []);
                $logs[] = array_merge([
                    'operation_type' => $operationType,
                    $this->idKey => $id,
                    'before_change' => json_encode($before),
                    'after_change' => json_encode($info),
                    'comment' => $comment,
                    'created_at' => Carbon::now()->toDateTimeString()
                ], $operationInfo);
            }
        }
        return $logs;
    }

    public function saveDeleteOpLogDatas($ids, $operationInfo, $comment = '')
    {
        $logs =$this->getDeleteOpLogDatas($ids, $operationInfo, $comment);
        return $this->insert($logs);
    }

    public function getDeleteOpLogDatas($ids, $operationInfo, $comment = '')
    {
        $logs = [];
        if ($ids && $operationInfo) {
            $before = ['id_del' => 0];
            $after = ['id_del' => 1];
            foreach ($ids as $id) {
                $logs[] = array_merge([
                    'operation_type' => config($this->opConfigKey . '.delete.code'),
                    $this->idKey => $id,
                    'before_change' => json_encode($before),
                    'after_change' => json_encode($after),
                    'comment' => $comment,
                    'created_at' => Carbon::now()->toDateTimeString()
                ], $operationInfo);
            }
        }
        return $logs;
    }
   
    //操作日志，不区分什么操作
    public function saveActionOpLogDatas($ids, $operationInfo, $beforeAndAfter, $comment = '')
    {
        $logs = $this->getActionOpLogDatas($ids, $operationInfo, $beforeAndAfter, $comment);
        return $this->insert($logs);
    }

    public function getActionOpLogDatas($ids, $operationInfo, $beforeAndAfter, $configOpType = 'update', $comment = '')
    {
        $logs = [];
        if ($ids && $operationInfo) {
            $before = Arr::get($beforeAndAfter, 'before', []);
            $after = Arr::get($beforeAndAfter, 'after', []);
            $createdAt = Carbon::now()->toDateTimeString();
            
            foreach ($ids as $id) {
                $logs[] = array_merge([
                    'operation_type' => config($this->opConfigKey . '.'. $configOpType .'.code'),
                    $this->idKey => $id,
                    'before_change' => json_encode($before),
                    'after_change' => json_encode($after),
                    'comment' => $comment,
                    'created_at' => $createdAt
                ], $operationInfo);
            }
        }
        return $logs;
    }
}
