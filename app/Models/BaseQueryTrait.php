<?php
namespace App\Models;

use App\Exceptions\NoStackException;
use Log;
use Illuminate\Support\Arr;


trait BaseQueryTrait
{

    /**
     * 通过ID获取一个实例数据
     *
     * @param $id 所查询数据的ID
     * @param $msg 未找到数据时抛出的异常MSG
     * @return array
     * @throws NoStackException
     */
    public function findById($id, $msg = null)
    {

        $query = $this->newQuery()->where('id', $id);
        $model = $query->first();

        if (empty($model)) {
            throw new NoStackException(empty($msg) ? sprintf('find_by_id model %s is empty id %s', get_called_class(), $id) : $msg);
        }

        $data = $model->toArray();

        Log::info(sprintf('find_by_id model %s id %s data %s', get_called_class(), $id, json_encode($data)));

        return $data;
    }

    /**
     * 获取满足条件的一条数据
     *
     * @param $conditions
     * @return mixed
     */
    public function findOne($conditions)
    {

        $query = $this->buildQueryByConditions($conditions);
        $model = $query->first();

        if (empty($model)) {
            return [];
        }

        $data = $model->toArray();

        Log::info(sprintf('find_one model %s conditions %s data %s', get_called_class(), json_encode($conditions), json_encode($data)));

        return $data;
    }

    /**
     * 分页查询一组数据
     *
     * @param $conditions
     * @param $page
     * @param $perPage
     * @param array $sortInfo
     * @return array
     */
    public function findList($conditions, $page, $perPage, $sortInfo = ['id' => 'asc'])
    {
        $page = (int)$page;
        $perPage = (int)$perPage;

        $countQuery = $this->buildQueryByConditions($conditions, $sortInfo);
        $pagingQuery = $this->buildPagingQueryByConditions($conditions, $page, $perPage, $sortInfo);

        $count = $countQuery->count();
        $list = $pagingQuery->get()->toArray();

        $totalPage = ceil($count / $perPage);

        $result = [
            'list' => $list,
            'pagination' => [
                'page' => $page,
                'perpage' => $perPage,
                'total_page' => $totalPage,
                'total_count' => $count,
            ]
        ];

        Log::info(sprintf(
            'find_list model %s conditions %s page %s per_page %s sort_info %s result %s',
            get_called_class(),
            json_encode($conditions),
            $page,
            $perPage,
            json_encode($sortInfo),
            json_encode($result)
        ));

        return $result;
    }

    /**
     * 查询所有满足条件的数据
     *
     * @param $conditions
     * @param array $sortInfo
     * @return mixed
     */
    public function findAll($conditions, $sortInfo = ['id' => 'asc'])
    {

        $query = $this->buildQueryByConditions($conditions, $sortInfo);

        $list = $query->get()->toArray();

        Log::info(sprintf(
            'find_all model %s conditions %s list %s',
            get_called_class(),
            json_encode($conditions),
            json_encode($list)
        ));

        return $list;
    }


    /**
     * 查询满足条件的数据条数
     *
     * @param $conditions
     * @return int
     */
    public function findCount($conditions)
    {

        $query = $this->buildQueryByConditions($conditions);
        $count = $query->count();

        Log::info(sprintf(
            'find_count model %s conditions %s count %s',
            get_called_class(),
            json_encode($conditions),
            $count
        ));

        return $count;
    }

    /**
     * 构建查询SQL, 如果查询条件为空则抛出异常,不查询
     *
     * @param $conditions
     * @param array $sortInfo
     * @return mixed
     * @throws NoStackException
     */
    private function buildQueryByConditions($conditions, $sortInfo = [])
    {

        if (sizeof($conditions) == 0) {
            throw new NoStackException(sprintf('conditions is empty model %s', get_called_class()));
        }

        $findable = empty($this->findable) ? [] : $this->findable;

        if (sizeof($findable) == 0) {
            throw new NoStackException(sprintf('findable is empty model %s', get_called_class()));
        }

        $query = $this->newQuery();
        $findableCount = 0;

        foreach ($conditions as $key => $value) {
            //是否在可查询字段内
            if (in_array($key, $findable)) {
                $findableCount++;
                if (is_array($value)) {
                    foreach ($value as $op => $vl) {
                        $op = strtolower($op);

                        if ($op == 'in') {
                            $query->whereIn($key, $vl);
                        } elseif ($op == 'gte') {
                            $query->where($key, '>=', $vl);
                        } elseif ($op == 'gt') {
                            $query->where($key, '>', $vl);
                        } elseif ($op == 'lte') {
                            $query->where($key, '<=', $vl);
                        } elseif ($op == 'lt') {
                            $query->where($key, '<', $vl);
                        } elseif ($op == 'like') {
                            $query->where($key, 'like', '%' . $vl . '%');
                        } elseif ($op == 'not_in') {
                            $query->whereNotIn($key, $vl);
                        }
                    }
                } else {
                    $query->where($key, $value);
                }
            }
        }

        if (!empty($sortInfo)) {
            foreach ($sortInfo as $column => $direction) {
                if (in_array($column, $findable)) {
                    $query->orderBy($column, $direction);
                }
            }
        }

        if ($findableCount == 0) {
            throw new NoStackException('findable_count is 0');
        }

        Log::info(sprintf(
            'build_query_by_conditions model %s condition %s sql %s',
            get_called_class(),
            json_encode($conditions),
            $query->toSql()
        ));

        return $query;
    }

    /**
     * 构建分页查询的 query
     *
     * @param $conditions
     * @param $page
     * @param $perPage
     * @param $sortInfo
     * @return mixed
     * @throws BeeperException
     */
    private function buildPagingQueryByConditions($conditions, $page, $perPage, $sortInfo = ['id' => 'asc'])
    {

        $query = $this->buildQueryByConditions($conditions, $sortInfo);

        $skip = $page > 1 ? ($page - 1) * $perPage : 0;
        $query->skip($skip)->take($perPage);

        Log::info(sprintf(
            'build_paging_query_by_conditions model %s condition %s page %s per_page %s sort_info %s sql %s',
            get_called_class(),
            json_encode($conditions),
            $page,
            $perPage,
            json_encode($sortInfo),
            $query->toSql()
        ));

        return $query;
    }

    //根据传入类型格式化数据
    private function formatDataType($type, $value)
    {

        switch ($type) {
            case 'time' : {
                return $value;
            }
            case 'int' : {
                return (int)$value;
            }
            case 'array' : {
                return explode(',', $value);
            }
            default: {
                return $value;
            }
        }
    }

    /**
     * 重构查询条件
     *
     * @param array $condition
     * @param array $fields
     * @return array
     */
    public function rebuildConditions(array $condition, array $fields)
    {

        $result = [];

        foreach ($fields as $field => $config) {
            if (is_array($config)) {
                $value = Arr::get($condition, $field);

                if (is_null($value)) {
                    continue;
                }

                if (isset($config['type'])) {
                    $type = Arr::get($config, 'type');
                    $value = $this->formatDataType($type, $value);
                }

                if (isset($config['to'])) {
                    $field = Arr::get($config, 'to');
                }

                if (isset($config['op'])) {
                    $op = Arr::get($config, 'op');
                    $va = array_get($result, $field, []);

                    $value = Arr::set($va, $op, $value);
                }

                Arr::set($result, $field, $value);
            } elseif (is_string($config)) {
                $value = Arr::get($condition, $config);

                if (is_null($value)) {
                    continue;
                }

                Arr::set($result, $config, $value);
            }
        }

        return $result;
    }
}
