<?php

namespace App\LoggerForums\Handlers;

use App\LoggerForums\Handlers\MonologHandler;

abstract class Handler
{
    /**
     * 日志输出类型
     *
     * @var string
     */
    protected $type;

    /**
     * 支持的日志输出模式类型
     *
     * @var array
     */
    protected $typeList = array('single', 'daily', 'hourly', 'fluentd');

    /**
     * 日志输出路径地址
     *
     * @var string
     */
    protected $path;


    /**
     * 日志输出级别
     *
     * @var string
     */
    protected $level;

    public static function getInstance($category = 'local')
    {
        // $driver = config('fenglog.driver');
        $appName = config('app.app_name', basename(base_path()));
        $category = $appName . '.' . $category;
        $handler = new MonologHandler($category);
        return $handler;
    }

    abstract public function setPath($path);

    abstract public function setType($type);

    abstract public function setLevel($level);

    /**
     * get hourly path to output
     *
     * @param  string $path origin path
     * @param  integer $hours split by hours
     * @return string  hourly path
     */
    protected function getHourlyPath($path, $hours = 1)
    {
        $fileInfo = pathinfo($path);
        $fileName = $fileInfo['dirname'] . DIRECTORY_SEPARATOR . $fileInfo['filename'] . '-' . date('Y-m-d');
        $hour = date('H');
        if ($hours >= 0 && $hours <= 24 && ($hour % $hours) !== 0) {
            $hour = $hour - ($hour % $hours);
        }
        $fileName .= '-' . $hour . 'h';
        if (isset($fileInfo['extension'])) {
            $fileName .= '.' . $fileInfo['extension'];
        }
        return $fileName;
    }
}
