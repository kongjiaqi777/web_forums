<?php

namespace App\Models;

use App\Exceptions\DatabaseException;
use App\Exceptions\NoStackException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Log;
use Illuminate\Support\Arr;
use DateTimeInterface;

class BaseModel extends Model
{
    //默认第一页
    const DEFAULT_PAGE = 1;

    //默认每页条数
    const DEFAULT_PERPAGE = 50;

    //最大每页条数
    const MAX_PERPAGE = 1000;

    //大于参数后缀
    const GREATER_THAN = '_gt';

    //大于等于参数后缀
    const GREATER_THAN_EQUAL = '_gte';

    //小于参数后缀
    const LESS_THAN = '_lt';

    //小于等于参数后缀
    const LESS_THAN_EQUAL = '_lte';

    //不等于参数后缀
    const NOT_EQUAL = '_ne';

    //模糊匹配参数后缀
    const LIKE = '_like';

    //排序方式
    const DIRECTION = ['asc', 'desc', 'ASC', 'DESC'];


    /**
     * 是否自动填充created_at和updated_at
     * @var bool
     */
    public $timestamps = true;

    /**
     * 可用于排序的字段集合
     * @var array
     */
    protected $sortable = [];

    /**
     * 可用于查询的字段集合
     * @var array
     */
    protected $findable = [];

    /**
     * 可用于插入的字段集合
     * @var array
     */
    protected $fillable = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * 构建查询条件
     * @param $condition
     * @param array $sortInfo
     * @return $this|\Illuminate\Database\Eloquent\Builder
     * @throws DatabaseException
     * @throws NoStackException
     */
    public function buildQueryCondition($condition, $sortInfo = ['id' => 'desc'])
    {
        if (empty($condition)) {
            throw new DatabaseException('Parameter cannot be null.');
        }

        if (empty($this->findable)) {
            throw new NoStackException('查询异常');
        }

        $query = $this->newQuery();

        $condition = Arr::except($condition, [
            'from',
            'sort',
            'page',
            'perpage',
            'operator_id',
            'operator_type',
            'operator_ip',
        ]);

        foreach ($condition as $key => $value) {
            if (!in_array($key, $this->findable)) {
                throw new NoStackException('不支持的查询字段:'.$key);
            }

            if ($value === null) {
                throw new NoStackException($key.'的值不能为null');
            }

            if (is_array($value)) {
                $query = $query->whereIn($key, $value);
            } elseif (strrchr($key, self::GREATER_THAN) === self::GREATER_THAN) {
                $query = $query->where(substr($key, 0, stripos($key, self::GREATER_THAN)), '>', $value);
            } elseif (strrchr($key, self::GREATER_THAN_EQUAL) === self::GREATER_THAN_EQUAL) {
                $query = $query->where(substr($key, 0, stripos($key, self::GREATER_THAN_EQUAL)), '>=', $value);
            } elseif (strrchr($key, self::LESS_THAN) === self::LESS_THAN) {
                $query = $query->where(substr($key, 0, stripos($key, self::LESS_THAN)), '<', $value);
            } elseif (strrchr($key, self::LESS_THAN_EQUAL) === self::LESS_THAN_EQUAL) {
                $query = $query->where(substr($key, 0, stripos($key, self::LESS_THAN_EQUAL)), '<=', $value);
            } elseif (strrchr($key, self::NOT_EQUAL) === self::NOT_EQUAL) {
                $query = $query->where(substr($key, 0, stripos($key, self::NOT_EQUAL)), '<>', $value);
            } elseif (strrchr($key, self::LIKE) === self::LIKE) {
                $query = $query->where(substr($key, 0, stripos($key, self::LIKE)), 'like', '%'.$value.'%');
            } else {
                $query = $query->where($key, $value);
            }
        }

        if (!empty($sortInfo)) {
            foreach ($sortInfo as $column => $direction) {
                if (in_array($column, $this->sortable) && in_array($direction, self::DIRECTION)) {
                    $query->orderBy($column, $direction);
                }
            }
        }

        return $query;
    }

    /**
     * 分页查询
     * @param $condition
     * @return array
     * @throws DatabaseException
     */
    public function getList($condition)
    {
        if (empty($condition)) {
            throw new DatabaseException('Parameter cannot be null.');
        }

        $page = Arr::get($condition, 'page', 1);
        $perpage = Arr::get($condition, 'perpage', 50);
        $sort = Arr::get($condition, 'sort', ['id' => 'desc']);

        $query = $this->buildQueryCondition($condition, $sort);

        return $this->overwritePagination($query, $page, $perpage);
    }

    /**
     * 构建分页
     * @param [type] $query
     * @param int $page
     * @param int $perpage
     * @param array $fields
     * @return void
     */
    public function overwritePagination($query, $page = 1, $perpage = 50, $fields = ['*'])
    {
        if (empty($query)) {
            throw new DatabaseException('Query cannot be null.');
        }

        $count = $query->distinct()->count($fields);

        $offset = $page > 1 ? ($page - 1) * $perpage : 0;
        
        $list = $query->offset($offset)->limit($perpage)->get($fields)->toArray();

        Log::info('SQL====='.$query->toSql());

        $totalPage = ceil($count / $perpage);

        $pagination = [
            'page' => (int)$page,
            'perpage' => (int)$perpage,
            'total_page' => $totalPage,
            'total_count' => $count,
        ];

        $result = [
            'list' => $list,
            'pagination' => $pagination,
        ];

        return $result;
    }

    /**
     * 查询全部数据(不分页)
     * @param $condition
     * @param array $sortInfo
     * @return array
     * @throws DatabaseException
     */
    public function getAll($condition, $sortInfo = ['id' => 'desc'], $fields = ['*'])
    {
        if (empty($condition)) {
            throw new DatabaseException('Parameter cannot be null.');
        }

        $query = $this->buildQueryCondition($condition, $sortInfo);

        return $query->select($fields)->get()->toArray();
    }

    /**
     * 按查询条件更新数据
     * @param $where
     * @param $updateData
     * @return bool|int
     * @throws DatabaseException
     */
    public function updateByCondition($where, $updateData, $throwException = true)
    {
        if (empty($where) || empty($updateData)) {
            throw new DatabaseException('Parameter cannot be null.');
        }

        $query = $this->buildQueryCondition($where);

        $result = $query->update($updateData);

        if (!$result && $throwException) {
            throw new DatabaseException('更新了0条数据');
        } else {
            return $result;
        }
    }

    /**
     * 插入一条数据
     * @param $fields
     * @return static
     * @throws DatabaseException
     */
    public function insertOne($fields)
    {
        if (empty($fields)) {
            throw new DatabaseException('Parameter cannot be null.');
        }

        $newInstance = $this->newInstance($fields);
        $newInstance->save();

        return $newInstance;
    }

    /**
     * 批量插入数据
     * @param $array
     * @throws DatabaseException
     */
    public function multiInsert($array)
    {
        if (empty($array)) {
            throw new DatabaseException('Parameter cannot be null.');
        }

        $time = Carbon::now()->toDateTimeString();

        $values = [];

        if ($this->usesTimestamps()) {
            foreach ($array as $fields) {
                if ($this->isFillable(static::CREATED_AT)) {
                    $fields[static::CREATED_AT] = $time;
                }

                if ($this->isFillable(static::UPDATED_AT)) {
                    $fields[static::UPDATED_AT] = $time;
                }

                array_push($values, $fields);
            }
        }

        return $this->insert($values);
    }

    /**
     * 根据ID获取一条数据
     * @param [type] $id
     * @param string $select
     * @return void
     */
    public function getById($id, $select = '*')
    {
        $result = $this->query()->selectRaw($select)->where('id', $id)->first();
        return empty($result) ? [] : $result->toArray();
    }

    /**
     * 按条件查询第一条数据
     * @param $where
     * @return Model|null|object|static
     * @throws DatabaseException
     */
    public function getFirstByCondition($where)
    {
        if (empty($where)) {
            throw new DatabaseException('Parameter cannot be null.');
        }

        $query = $this->buildQueryCondition($where);

        $result = $query->first();

        return empty($result) ? [] : $result->toArray();
    }

    /**
     * 获取满足条件的一条数据
     *
     * @param $conditions
     * @return mixed
     */
    public function findOne($conditions)
    {

        $query = $this->buildQueryCondition($conditions);
        $model = $query->first();

        if (empty($model)) {
            return [];
        }

        $data = $model->toArray();

        Log::info(sprintf('find_one model %s conditions %s data %s', get_called_class(), json_encode($conditions), json_encode($data)));

        return $data;
    }

    /**
     * 获取分页变量
     * @param [type] $fields
     * @param [type] $query
     * @param [type] $page
     * @param [type] $perPage
     * @param bool $withItems
     * @return void
     */
    public function getPaginate($fields, $query, $page, $perPage, $withItems = false)
    {
        if ($perPage && $page) {
            $paginator = $query->paginate($perPage, $fields, 'page', $page);
            $paginate = [
                'page' => $paginator->currentPage(),
                'perpage' => $paginator->perPage(),
                'total_page' => $paginator->lastPage(),
                'total_count' => $paginator->total(),
            ];
            if ($withItems) {
                $items = $paginator->items();
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if ($item) {
                            if (is_object($item)) {
                                $result['list'][] = $item->toArray();
                            } else {
                                $result['list'][] = $item;
                            }
                        } else {
                            $result['list'][] = [];
                        }
                    }
                } else {
                    $result['list'] = [];
                }
                
                $result['pagination'] = $paginate;
                return $result;
            }
        }
        return $paginate;
    }

    public function getFindable()
    {
        return $this->findable;
    }

    public function getUpdateable()
    {
        return $this->updateable;
    }
}
