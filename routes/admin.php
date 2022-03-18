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
$router->group(['prefix' => 'v1/admin/user'], function () use ($router) {
    // 登录
    $router->post('login', ['uses' => 'Admin\AdminUserController@login']);

    // 登出
    $router->post('logout', ['uses' => 'Admin\AdminUserController@logout']);

    // 添加管理端用户
    $router->post('signup', ['uses' => 'Admin\AdminUserController@signup']);

    $router->get('suggest', ['uses' => 'Admin\AdminUserController@suggest']);
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
    // 列表
    $router->get('list', ['uses' => 'Admin\AdminPostController@list']);

    // 详情
    $router->get('detail', ['uses' => 'Admin\AdminPostController@detail']);

    // 设置置顶
    $router->post('set_top', ['uses' => 'Admin\AdminPostController@setTop']);

    // 删除广播
    $router->post('delete_post', ['uses' => 'Admin\AdminPostController@deletePost']);

    // 删除回复
    $router->post('delete_reply', ['uses' => 'Admin\AdminPostController@deleteReply']);

    $router->get('reply_list', ['uses' => 'Admin\AdminPostController@getListWithoutSub']);

    // 模糊搜索
    $router->get('suggest', ['uses' => 'Admin\AdminPostController@suggest']);
});

// 管理端用户-投诉相关路由
$router->group(['prefix' => 'v1/admin/complaint', 'middleware' => 'admin'], function () use ($router) {
    // 广播投诉列表
    $router->get('post_list', ['uses' => 'Admin\AdminComplaintController@getPostComplaintList']);
    
    // 广场主投诉列表
    $router->get('user_list', ['uses' => 'Admin\AdminComplaintController@getUserComplaintList']);
    
    // 投诉详情
    $router->get('detail', ['uses' => 'Admin\AdminComplaintController@detail']);
    
    // 处理广播投诉
    $router->post('deal_post', ['uses' => 'Admin\AdminComplaintController@dealPost']);

    // 处理广场主投诉
    $router->post('deal_square_owner', ['uses' => 'Admin\AdminComplaintController@dealSquareOwner']);
});
