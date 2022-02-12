<?php

/*
|--------------------------------------------------------------------------
| Web Routes For Admin User
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 管理端用户-登录相关路由
$router->group(['prefix' => 'v1/admin/user', 'middleware' => 'admin'], function () use ($router) {
    // 登录
    $router->post('login', ['uses' => 'AdminUserController@login']);

    // 登出
    $router->post('logout', ['uses' => 'AdminUserController@logout']);

    // 添加管理端用户
    $router->post('add', ['uses' => 'AdminUserController@addAdminUser']);
    
    // 管理端用户列表
    $router->get('list', ['uses' => 'AdminUserController@list']);
});

// 管理端用户-广场相关路由
$router->group(['prefix' => 'v1/admin/square', 'middleware' => 'admin'], function () use ($router) {
    // 广场列表
    $router->get('list', ['uses' => 'Admin\AdminSquareController@list']);

    // 广场详情
    $router->get('detail', ['uses' => 'Admin\AdminSquareController@detail']);

    // 更新广场信息
    $router->post('update', ['uses' => 'Admin\AdminSquareController@update']);

    // 删除广场
    $router->post('delete', ['uses' => 'Admin\AdminSquareController@delete']);

    // 审核通过广场创建申请
    $router->post('approve', ['uses' => 'Admin\AdminSquareController@approve']);

    // 审核驳回广场创建申请
    $router->post('reject', ['uses' => 'Admin\AdminSquareController@reject']);

    // 更换广场主
    $router->post('switch', ['uses' => 'Admin\AdminSquareController@switch']);

    // 模糊搜索
    $router->get('suggest', ['uses' => 'Admin\AdminSquareController@suggest']);

    // 驳回更换广场主申请
    $router->post('reject_switch', ['uses' => 'Admin\AdminSquareController@rejectSwitch']);
});

// 管理端用户-广播相关路由
$router->group(['prefix' => 'v1/admin/post', 'middleware' => 'admin'], function () use ($router) {
});

// 管理端用户-投诉相关路由
$router->group(['prefix' => 'v1/admin/complaint', 'middleware' => 'admin'], function () use ($router) {
    $router->get('post_list', ['uses' => 'Admin\AdminComplaintController@getPostComplaintList']);
    $router->get('user_list', ['uses' => 'Admin\AdminComplaintController@getUserComplaintList']);
    $router->get('detail', ['uses' => 'Admin\AdminComplaintController@detail']);
    $router->post('deal', ['uses' => 'Admin\AdminComplaintController@deal']);

});

// 管理端用户-用户管理相关路由
