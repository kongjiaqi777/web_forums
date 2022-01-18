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
    $router->get('list', ['uses' => 'AdminSquareController@list']);

    // 广场详情
    $router->get('detail', ['uses' => 'AdminSquareController@detail']);

    // 更新广场信息
    $router->post('update', ['uses' => 'AdminSquareController@update']);

    // 删除广场
    $router->post('delete', ['uses' => 'AdminSquareController@delete']);

    // 审核通过广场创建申请
    $router->post('approve', ['uses' => 'AdminSquareController@approve']);

    // 审核驳回广场创建申请
    $router->post('reject', ['uses' => 'AdminSquareController@reject']);

    $router->get('detail', ['uses' => 'AdminSquareController@']);
    $router->get('detail', ['uses' => 'AdminSquareController@']);
    $router->get('detail', ['uses' => 'AdminSquareController@']);
    $router->get('detail', ['uses' => 'AdminSquareController@']);
    $router->get('detail', ['uses' => 'AdminSquareController@']);
    $router->get('detail', ['uses' => 'AdminSquareController@']);
});

// 管理端用户-广播相关路由

// 管理端用户-投诉相关路由

// 管理端用户-用户管理相关路由
