<?php

return [


    /**
     * header name
     */
    'trace_id' => 'request-id',
    // 'HTTP_X_REQUEST_ID',

    'default_format' => '#traceId:{traceId} operator_id:{operatorId} operator_type:{operatorType} host:{host} from:{from} uri:{uri} clientIp:{clientIp} ',

    'request_log' => [

        'enabled' => true,

        'level'   => env('APP_LOG_LEVEL', 'debug'),

        'path'   => env('APP_REQUEST_LOG_PATH', storage_path('logs/request/request.log')),

        /**
         * Available Settings: "single", "daily", "hourly"
         * if set hourly, please config APP_REQUEST_LOG_HOURLY in .env file, default value 1
         * Example: APP_REQUEST_LOG_HOURLY=1
         */
        'type'   => env('APP_REQUEST_LOG_TYPE', 'daily'),
        'format' => 'totalTime:{totalTime} asyncTime:{asyncTime} dbCount:{dbCount} dbTime:{dbTime} memPeakUsage:{memPeakUsage} occurTime:{occurTime} requestParams:{requestParams} headerInfo:{headerInfo}'
    ],

    'base_log' => [

        'enabled' => true,

        'async' => true,

        'level'   => env('APP_LOG_LEVEL', 'debug'),

        'path' => env('APP_BASE_LOG_PATH', storage_path('logs/base/base.log')),

        /**
         * Available Settings: "single", "daily", "hourly"
         */
        'type' => env('APP_BASE_LOG_TYPE', 'daily'),

        'format' => 'asyncTime:{asyncTime} occurTime:{occurTime} file:{file} line:{line} message:[{message}]'
    ],

    'exception_log' => [

        'enabled' => true,

        'level'   => env('APP_LOG_LEVEL', 'debug'),

        'path' => env('APP_EXCEPTION_LOG_PATH', storage_path('logs/error/error.log')),

        /**
         * Available Settings: "single", "daily", "hourly"
         */
        'type' => env('APP_EXCEPTION_LOG_TYPE', 'daily'),

        'format' => 'totalTime:{totalTime} asyncTime:{asyncTime} file:{file} line:{line} error:[{error}]'
    ],

    'sql_log'       => [

        'enabled' => true,

        'level'   => env('APP_LOG_LEVEL', 'debug'),

        'path' => env('APP_SQL_LOG_PATH', storage_path('logs/sql/sql.log')),

        /**
         * Available Settings: "single", "daily", "hourly"
         */
        'type' => env('APP_SQL_LOG_TYPE', 'daily'),

        'format' => 'time:{time} occurTime:{occurTime} sql:[{sql}]'
    ],

    'trans_log'     => [

        'enabled' => false,

        'level'   => env('APP_LOG_LEVEL', 'debug'),

        'path' => env('APP_TRANS_LOG_PATH', storage_path('logs/trans/trans.log')),

        /**
         * Available Settings: "single", "daily", "hourly"
         */
        'type' => env('APP_TRANS_LOG_TYPE', 'daily'),

        'format' => 'DB TRANSACTION:[{sql}]'
    ],

    'custom_log'     => [
        'level'   => env('APP_LOG_LEVEL', 'debug'),
    ],

    /**
     * ????????????????????????
     */
    'filter'        => [
        'password',
        'passwd',
        'pass'
    ],

    /*
     * ???????????????
     */
    'warning_tag' => [
        'business' => '????????????',
    ],

];
