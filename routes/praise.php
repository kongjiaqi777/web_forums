<?php

/*
|--------------------------------------------------------------------------
| Web Routes About Praise
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->group(['prefix' => 'v1/praise', 'middleware' => 'api'], function () use ($router) {
    // 点赞广播
    $router->post('create_post', 'PraiseController@createPost');

    // 取消点赞广播
    $router->post('cancel_post', 'PraiseController@cancelPost');

    // 点赞评论
    $router->post('create_reply', 'PraiseController@createReply');

    // 取消点赞评论
    $router->post('cancel_reply', 'PraiseController@cancelReply');
});
 