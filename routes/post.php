<?php

/*
|--------------------------------------------------------------------------
| Web Routes About Post
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->group(['prefix' => 'v1/post', 'middleware' => 'api'], function () use ($router) {
    // 广播列表
    $router->get('list', ['uses' => 'PostController@list']);

    // 创建广播
    $router->post('create',['uses' => 'PostController@create']);

    // 更新广播
    $router->post('update', ['uses' => 'PostController@update']);

    // 广场主置顶
    $router->post('set_top', ['uses' => 'PostController@setTop']);

    // 删除广播
    $router->post('delete', ['uses' => 'PostController@delete']);

    // 广播详情
    $router->get('detail', ['uses' => 'PostController@detail']);

    // 添加浏览记录
    $router->post('add_browse', ['uses' => 'PostController@addBrowseRecord']);

    // 浏览历史
    $router->get('browse_list', ['uses' => 'PostController@browseList']);

    // 查看广场内置顶的广播列表
    $router->get('top_list', ['uses' => 'PostController@getTopList']);

    // 我的广播列表
    $router->get('my_list', ['uses' => 'PostController@getMyPostList']);

    $router->get('suggest', ['uses' => 'PostController@suggest']);
    $router->post('cancel_top', ['uses' => 'PostController@cancelTop']);
});


$router->group(['prefix' => 'v1/reply', 'middleware' => 'api'], function () use ($router) {
    // 回复列表
    $router->get('list', ['uses' => 'ReplyController@list']);

    // 添加广播评论
    $router->post('create', ['uses' => 'ReplyController@create']);

    // 回复广播评论
    $router->post('create_sub', ['uses' => 'ReplyController@createSub']);

    // 删除广播评论
    $router->post('delete', ['uses' => 'ReplyController@delete']);

    // 查看某一楼的全部回复
    $router->get('sub_list', ['uses' => 'ReplyController@getSubList']);

    // 查看我的回复列表
    $router->get('my_list', ['uses' => 'ReplyController@getMyReplyList']);
});

