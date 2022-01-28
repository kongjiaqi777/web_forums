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

    // 
    $router->get('follow_list', ['uses' => 'UserController@myFollowUserList']);
    $router->post('set_follow', ['uses' => 'UserController@setFollowUser']);
    $router->post('cancel_follow', ['uses' => 'UserController@cancelFollowUser']);
    $router->get('fans_list', ['uses' => 'UserController@myFansUserList']);
    $router->get('get_by_id', ['uses' => 'UserController@getUserInfoById']);
    // 查询用户信息
    $router->get('suggest_user', ['uses' => 'UserController@suggestUser']);
});
$router->group(['prefix' => 'v1/common'], function () use ($router) {
    $router->get('config', ['uses' => 'ConfigController@getConfigList']);
});


