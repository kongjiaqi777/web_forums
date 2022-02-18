<?php

namespace App\Libs;

use Illuminate\Support\Arr;

class UtilLib
{
    /**
     * index by 将一个数组转换成按指定字段作为key的新数组
     * @param $array [[id=>1], [id=>2], [id=>3], ...]
     * @param $key id
     * @return array [1=>[id=>1], 2=>[id=>2], 3=>[id=>3], ...]
     */
    public static function indexBy($array, $key)
    {
        if (empty($array) || empty($key)) {
            return array();
        }

        $result = array();

        foreach ($array as $item) {
            if (is_array($item)) {
                $index = Arr::get($item, $key, null);
            } elseif (is_object($item)) {
                $index = object_get($item, $key, null);
            } else {
                continue;
            }

            if (is_null($index)) {
                continue;
            }

            $result[$index] = $item;
        }

        return $result;
    }


    /**
     * 按指定key给一个array分组
     * @param $array [[id=>1,value=100], [id=>2,value=>200], [id=>1,value=>200], ...]
     * @param $key id
     * @return array [1=>[[id=>1,value=100], [id=>1,value=>200]], 2=>[[id=>2,value=>200]]]
     */
    public static function groupBy($array, $key)
    {
        if (empty($array) || empty($key)) {
            return array();
        }

        $result = array();

        foreach ($array as $item) {
            if (is_array($item)) {
                $index = Arr::get($item, $key, null);
            } elseif (is_object($item)) {
                $index = object_get($item, $key, null);
            } else {
                continue;
            }

            if (is_null($index) || empty($index)) {
                continue;
            }

            if (isset($result[$index])) {
                $list = $result[$index];
            } else {
                $list = array();
            }

            array_push($list, $item);
            $result[$index] = $list;
        }

        return $result;
    }


    public static function getConfigByCode($code, $configName, $field = null, $default = null)
    {

        if (is_string($configName)) {
            $config = config($configName);
        } else {
            $config = $configName;
        }
        
        $result = $default;

        foreach ($config as $key => $eachConfig) {
            if ($code == Arr::get($eachConfig, 'code')) {
                $result['_key'] = $key;
                $result = $eachConfig;
                break;
            }
        }

        if (empty($field)) {
            return $result;
        } else {
            return Arr::get($result, $field, $default);
        }
    }

    public static function getDataInField($data, $fieldList)
    {
        $fieldData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $fieldList)) {
                $fieldData[$key] = $value;
            }
        }
        return $fieldData;
    }

    public static function getDiffData($origin, $new)
    {
        $diff = [];
        foreach ($new as $key => $value) {
            $originValue = $origin[$key] ?? '';
            if ($originValue != $value) {
                $diff [$key] = $value;
            }
        }
        return $diff;
    }

}
