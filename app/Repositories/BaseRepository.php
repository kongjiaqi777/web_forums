<?php

namespace App\Repositories;


use DB;
use Carbon\Carbon;
use Log;
use App\Exceptions\BaseException;
use App\Exceptions\NoStackException;
use Illuminate\Support\Arr;

abstract class BaseRepository
{
    /*
     * 过滤查询
     * $model-Model名称
     * $cons-所有$request参数列表
     * $consSearch-在model中规定的可作为查询条件的字段
     * $field-默认查询字段
     * */
    protected function getQueryBuilder($model, $conds, $condsSearch, $fields = ['*'], $table_prefix = '', $skipOrder = false)
    {
        $query = $model->select(DB::raw(implode(',', $fields)));

        return $this->getQuery($query, $conds, $condsSearch, $table_prefix, $skipOrder);
    }

    /**
     * 查询筛选方法
     * @param [type] $query
     * @param [type] $conds
     * @param [type] $condsSearch
     * @param string $table_prefix
     * @param bool $skipOrder
     * @return void
     */
    protected function getQuery($query, $conds, $condsSearch, $table_prefix = '', $skipOrder = false)
    {
        if ($query && $conds && $condsSearch) {
            foreach ($condsSearch as $condsKey => $condsValue) {
                $queryKey = Arr::get($condsValue, 'query_key', '');
                $operator = Arr::get($condsValue, 'operator', '');
                $searchVal = Arr::get($conds, $condsKey, '');
                if (is_string($searchVal)) {
                    $searchVal = trim($searchVal);
                }

                if ($queryKey && $operator) {
                    switch ($operator) {
                        case '=':
                            $query->where($table_prefix.$queryKey, '=', $searchVal);
                            break;
                        case 'in':
                            if (is_array($searchVal)) {
                                $query->whereIn($table_prefix.$queryKey, $searchVal);
                            }
                            break;

                        case 'like':
                            $query->where($table_prefix.$queryKey, $operator, '%' . $searchVal . '%');
                            break;

                        case 'between':
                            if (!is_array($searchVal)) {
                                $searchVal = explode(',', $searchVal);
                            }
                            $leftMinVal = $searchVal[0];
                            $rightMaxVal = $searchVal [1];
                            if ($leftMinVal && $rightMaxVal) {
                                $query->whereBetween($table_prefix.$queryKey, [$leftMinVal, $rightMaxVal]);
                            }
                            break;
                        default:
                            $query->where($table_prefix.$queryKey, $operator, $searchVal);
                            break;
                    }
                }
            }
        }
        if (!$skipOrder) {
            $orderBy = Arr::get($conds, 'order_field', $table_prefix.'id');
            $orderByRule = Arr::get($conds, 'order_field_rule', 'desc');
            $query = $query->orderByRaw($orderBy . ' ' . $orderByRule . ',' . $table_prefix .'id ' . $orderByRule);
        }
        
        return $query;
    }

    /**
     * 对比待更新的数据和原始数据，只保留有更改的字段,不考虑未传字段的情况
     * @param [type] $new
     * @param [type] $original
     * @return void
     */
    public function getUpdateData($new, $original)
    {
        $result = [];
        if ($new && $original) {
            foreach ($new as $key => $newVal) {
                $oriVal = Arr::get($original, $key);
                if (is_string($oriVal)) {
                    $oriVal = trim($oriVal, '"');
                }
                if ($newVal != $oriVal) {
                    $result['new'][$key] = $newVal;
                    $result['original'][$key] = $oriVal;
                }
            }
        }
        return $result;
    }

    /**
     * create方法之前的字段处理
     *
     * @param [type] $data
     * @param [type] $fillable
     * @param array $jsonable
     * @return void
     */
    protected function prepareCreate($data, $fillable, $jsonable = [])
    {
        $info = [];
        if (!empty($data) && !empty($fillable)) {
            $info = array_intersect_key($data, $fillable);
            $info['created_at'] = Carbon::now()->toDateTimeString();
            $info['updated_at'] = Carbon::now()->toDateTimeString();
            if (!empty($jsonable)) {
                foreach ($jsonable as $keyForJson) {
                    $arr = Arr::get($data, $keyForJson, '');
                    if (is_array($arr)) {
                        $info[$keyForJson] = json_encode($arr);
                    }
                }
            }
        }
        return $info;
    }

    /**
     * update方法之前的字段处理
     *
     * @param [type] $data
     * @param [type] $updateable
     * @param array $jsonable
     * @return void
     */
    protected function prepareUpdate($data, $updateable, $jsonable = [])
    {
        $info = [];
        if (!empty($data) && !empty($updateable)) {
            $info = array_intersect_key($data, $updateable);
            if (!empty($jsonable)) {
                foreach ($jsonable as $keyForJson) {
                    $arr = Arr::get($data, $keyForJson, '');
                    if (is_array($arr)) {
                        $info[$keyForJson] = json_encode($arr);
                    }
                }
            }
        }
        return $info;
    }

    /**
     * 通过查询方法
     *
     * @param [type] $searchModel
     * @param [type] $fields
     * @param [type] $conds
     * @param integer $page
     * @param integer $perpage
     * @param [type] $leftModels
     * @return void
     */
    public function getDataList($searchModel, $fields, $conds, $page=1, $perpage=20, $leftModels=null, $sortInfo = ['id' => 'desc'])
    {
        $searchAble = $searchModel->getSearchAble();
        $condsSearch = array_intersect_key($searchAble, $conds);

        $query = $searchModel;
        $leftTableName = $searchModel->getTable();

        if ($leftModels) {
            foreach ($leftModels as $leftModel) {
                $leftModelName = Arr::get($leftModel, 'table_name', '');
                $leftModelLeft = Arr::get($leftModel, 'left', '');
                $leftModelRight = Arr::get($leftModel, 'right', '');
                $leftModelConds = Arr::get($leftModel, 'conds', []);
                $leftModelCondsSearch = Arr::get($leftModel, 'conds_search', []);

                $query = $query->leftJoin($leftModelName, $leftModelLeft, '=', $leftModelRight);
                $query = $this->getQuery($query, $leftModelConds, $leftModelCondsSearch, $leftModelName . '.', true);
            }
        }

        if ($condsSearch) {
            $query = $this->getQueryBuilder($query, $conds, $condsSearch, $fields, $leftTableName . '.', true);
        }

        $offset = ($page - 1) * $perpage;
        $pagination = $searchModel->getPaginate($fields, $query, $page, $perpage);

        $query = $query->select($fields)->offset($offset)->limit($perpage);

        if (!empty($sortInfo)) {
            foreach ($sortInfo as $column => $direction) {
                if (in_array($column, $searchModel->sortable) && in_array($direction, $searchModel::DIRECTION)) {
                    $query = $query->orderBy($column, $direction);
                }
            }
        }

        $list = $query->get()->all();

        return [
            'list' => $list,
            'pagination' => $pagination
        ];

    }

    /**
     * 通用创建，有OpLog
     *
     * @param object  $insertModel         创建Model
     * @param array   $insertData          插入的数据
     * @param object  $insertOpLogModel    创建LogModel
     * @param array   $operationInfo       操作信息
     * @param string  $message             提示信息
     * @return void
     */
    public function commonCreate($insertModel, $insertData, $insertOpLogModel, $operationInfo, $message='')
    {
        $fillable = array_fill_keys($insertModel->getFillable(), 1);
        $insertInfo = $this->prepareCreate($insertData, $fillable);

        $res = DB::transaction(function () use ($insertModel, $insertOpLogModel, $insertInfo, $operationInfo, $message) {
            try{
                $insertId = $insertModel->insertGetId($insertInfo);
                $insertOpLogModel->saveCreateOpLogDatas(
                    [
                        $insertId => $insertInfo
                    ],
                    $operationInfo,
                    $message
                );
                return $insertId;
            } catch (\Exception $e) {
                Log::error(sprintf($message.'失败[Params][%s][Code][%s][Message][%s]',json_encode($insertInfo), $e->getCode(), $e->getMessage()));
                throw new BaseException($message.'失败');
            }
            
        });
        return $res;
    }

    /**
     * 通用创建，无OpLog
     * @param [type] $insertModel
     * @param [type] $insertData
     * @return void
     */
    public function commonCreateNoLog($insertModel, $insertData)
    {
        $fillable = array_fill_keys($insertModel->getFillable(), 1);
        $insertInfo = $this->prepareCreate($insertData, $fillable);

        return $insertModel->insertGetId($insertInfo);
    }

    /**
     * 通用更新
     *
     * @param int       $dataId         需要更新的数据ID
     * @param object    $updateModel    更新Model
     * @param object    $updateLogModel 更新LogModel
     * @param array     $updateData     需要更新的数据
     * @param array     $operationInfo  操作信息
     * @param string    $message        提示信息
     * @return void
     */
    public function commonUpdate($dataId, $updateModel, $updateLogModel, $updateData, $operationInfo, $message='更新', $operationTypeSpec='update', $isNeedOpLog=true)
    {
        $oriInfo = $updateModel->getById($dataId);

        // 验证
        if (!$oriInfo) {
            throw new NoStackException('信息不存在');
        }

        // 准备数据
        $updateable = array_fill_keys($updateModel->getUpdateable(), 1);
        $updateInfo = $this->prepareUpdate($updateData, $updateable);
        $originalInfo = $this->prepareUpdate($oriInfo, $updateable);
        $updateInfoForUp = $this->getUpdateData($updateInfo, $originalInfo);
        
        // 更新
        $res = DB::transaction(function () use ($dataId, $updateModel, $updateLogModel, $updateInfoForUp, $operationInfo, $message, $operationTypeSpec, $isNeedOpLog) {
            $updateNew = Arr::get($updateInfoForUp, 'new', []);
            $updateOri = Arr::get($updateInfoForUp, 'original', []);
            if ($updateNew && $updateOri) {
                try {
                    $updateRes = $updateModel->where('id', $dataId)->update($updateNew);
                    if ($isNeedOpLog) {
                        $updateLogModel->saveUpdateOpLogDatas(
                            [$dataId => $updateNew],
                            [$dataId => $updateOri],
                            $operationInfo,
                            $message,
                            $operationTypeSpec
                        );
                    }
                   
                    return $updateRes;
                } catch (\Exception $e) {
                    Log::info(sprintf($message.'信息失败[Code][%s][Msg][%s][Input][%s]', $e->getCode(), $e->getMessage(), json_encode($updateInfoForUp)));
                    throw new NoStackException($message.'信息失败');
                }
            }
           
        });

        return $res;
    }
}
