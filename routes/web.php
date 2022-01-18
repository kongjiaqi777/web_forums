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

$router->group(['prefix' => 'v1/user/', 'middleware' => 'api'], function () use ($router) {
    // 根据token获取用户信息
    $router->post('info', ['uses' => 'UserController@getUserInfoById']);

    // 修改用户信息
    $router->post('update_info', ['uses' => 'UserController@updateInfo']);

    // 修改用户头像
    $router->post('update_avatar', ['uses' => 'UserController@updateAvatar']);

    // 查询用户信息
    $router->post('suggest_user', ['uses' => 'UserController@suggestUser']);
});

