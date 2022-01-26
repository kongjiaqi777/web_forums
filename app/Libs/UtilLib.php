<?php

namespace App\Libs;

use Carbon\Carbon;
use Log;
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

    public static function isMobile($string)
    {
        $pattern = '/^1[3456789]{1}\d{9}$/';
        if (preg_match($pattern, $string, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isCarNum($string)
    {
        //普通车辆
        $pattern = '/^[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[A-Z0-9]{5}$/u';
        //小型新能源车
        $smallElectronic = '/^[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[DF]{1}[A-Z0-9]{5}$/u';
        //大型新能源车
        $bigElectronic = '/^[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[A-Z0-9]{5}[DF]{1}$/u';
        if (preg_match($pattern, $string, $matches)) {
            return true;
        } elseif (preg_match($smallElectronic, $string, $matches)) {
            return true;
        } elseif (preg_match($bigElectronic, $string, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isIdCardNum($string)
    {
        $pattern = '/^[1-9]{1}\d{16}[0-9X]{1}$/';
        if (preg_match($pattern, $string, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isBankCardId($string)
    {
        $pattern = '/^[1-9]{1}\d{14,18}$/';
        if (preg_match($pattern, $string, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    public static function removeExtraLogInfo($data, $extra = [])
    {
        $field = [
            '_url',
            'operator_id',
            'operator_name',
            'operation_source',
            'operator_ip',
            'operator_type',
            'from',
            'operation_from',
        ];
        $field = array_merge($field, $extra);
        $updateData = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $field)) {
                $updateData[$key] = $value;
            }
        }
        return $updateData;
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


    /**
     * 将秒转换为小时
     * @param int $seconds
     * @param string $format string *小时*分*秒 float  *.**小时
     * @return mixed
     */
    public static function formatSecondsToHours($seconds, $format = 'string')
    {
        $formatRes = 0;
        if (!is_null($seconds) && $format) {
            if ($format == 'string') {
                $hours = floor($seconds/3600);
                $minutes = floor(($seconds%3600)/60);
                $secs = $seconds%60;
                $formatRes = sprintf('%d小时%d分%d秒', $hours, $minutes, $secs);
            } else {
                $formatRes = round($seconds/3600, 2);
            }
        }
        return $formatRes;
    }
}
