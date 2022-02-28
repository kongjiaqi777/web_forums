<?php

/*
|--------------------------------------------------------------------------
| Web Routes About Square For Common User
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->group(['prefix' => 'v1/square', 'middleware' => 'api'], function () use ($router) {
    // 广场列表
    $router->get('list', ['uses' => 'SquareController@getList']);

    // 我关注的广场列表
    $router->get('my_follow_list', ['uses' => 'SquareController@myFollowList']);

    // 广场suggest
    $router->get('suggest', ['uses' => 'SquareController@suggest']);
    
    // 广场详情
    $router->get('detail', ['uses' => 'SquareController@detail']);
    
    // 创建广场
    $router->post('create', ['uses' => 'SquareController@create']);
    
    // 修改广场
    $router->post('update', ['uses' => 'SquareController@update']);
    
    // 关注广场
    $router->post('set_follow', ['uses' => 'SquareController@setFollow']);
    
    // 取消关注
    $router->post('cancel_follow', ['uses' => 'SquareController@cancelFollow']);
    
    // 广场主申请卸任
    $router->post('apply_relieve', ['uses' => 'SquareController@applyRelieve']);
});



