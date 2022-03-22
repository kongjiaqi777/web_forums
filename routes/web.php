<?php

/*
|--------------------------------------------------------------------------
| Web Routes About Users
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->group(['prefix' => 'v1/user', 'middleware' => 'api'], function () use ($router) {
    // 根据token获取用户信息
    $router->post('info', ['uses' => 'UserController@getUserInfoByToken']);

    // 修改用户信息
    $router->post('update_label', ['uses' => 'UserController@updateLabel']);

    // 我关注的用户列表
    $router->get('follow_list', ['uses' => 'UserController@myFollowUserList']);
    
    // 关注某人
    $router->post('set_follow', ['uses' => 'UserController@setFollowUser']);
    
    // 取关某人
    $router->post('cancel_follow', ['uses' => 'UserController@cancelFollowUser']);
    
    // 关注我的人列表
    $router->get('fans_list', ['uses' => 'UserController@myFansUserList']);
    
    // 获取用户详情
    $router->get('get_by_id', ['uses' => 'UserController@getUserInfoById']);
    
    // 查询用户信息
    $router->get('suggest_user', ['uses' => 'UserController@suggestUser']);
    
    // 登出
    $router->post('logout', ['uses' => 'UserController@logout']);
});

$router->group(['prefix' => 'v1/common'], function () use ($router) {
    // 配置列表
    $router->get('config', ['uses' => 'ConfigController@getConfigList']);
    $router->get('get_qiniu_token', ['uses' => 'QiniuController@getQiniuToken']);
});

$router->group(['prefix' => 'v1/message', 'middleware' => 'api'], function () use ($router) {
    // 消息列表
    $router->get('list', ['uses' => 'MessageController@myMessageList']);

    // 消息详情
    $router->get('detail', ['uses' => 'MessageController@detail']);

    // 标记已读
    $router->post('read', ['uses' => 'MessageController@read']);

    // 删除
    $router->post('delete', ['uses' => 'MessageController@delete']);
});

$router->group(['prefix' => 'v1/complaint', 'middleware' => 'api'], function () use ($router) {
    // 投诉详情
    $router->get('detail', ['uses' => 'ComplaintController@detail']);

    // 添加投诉
    $router->post('create', ['uses' => 'ComplaintController@create']);
});


