<?php

/*
|--------------------------------------------------------------------------
| Web Routes For Follow
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 用户关注相关
$router->group(['prefix' => 'v1/follow_user', 'middleware' => 'api'], function () use ($router) {
});

