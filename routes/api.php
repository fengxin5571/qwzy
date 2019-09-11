<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');
/*v1小程序接口*/
$api->version('v1',[
    'namespace'=>'App\Http\Controllers\Api',
    'middleware'=>['bindings','cors'],
], function($api) {
    //供货商
    $api->group(['prefix'=>'sup'],function ($api){
        //获取供货商注册配置信息
        $api->get('register/setting','AuthController@setting');
        //供应商注册
        $api->post('register','AuthController@register')->name('sup.register');
        //供应商登录
        $api->post('login','AuthController@login')->name('sup.login');
        $api->get('test','AuthController@test');
        //供应商微信快捷登录
        $api->post('miniLogin','AuthController@routeLogin');
        //获取openid
        $api->get('/weixin/openid','AuthController@getOpenId');
        //获取小程序码
        $api->get('/getcode','AuthController@getCode');


        $api->group(['middleware'=>'authToken'],function ($api){
            //个人中心
            $api->get('my','SupplierController@my');
            //供应商微信绑定
            $api->post('/weixin/bind','SupplierController@bind');
            //解除微信绑定
            $api->post('/weixin/unbind','SupplierController@unbind');
            //退出登录
            $api->post('loginout','SupplierController@loginout');
            //我的通知
            $api->get('notice','SupplierController@notice');
            //供货商预约进度
            $api->get('progress','SupplierController@progress');
            //供货商预约进度详情
            $api->get('progress/show','SupplierController@progressDetails');
            //我的供货记录
            $api->get('/my/supply','SupplierController@mySupply');
        });
    });
    //企业通知
    $api->group(['prefix'=>'article'],function ($api){
        //文章分类
        $api->get('/category','ArticleController@category');
        //文章标签
        $api->get('/tag','ArticleController@tag');
        //热门搜索
        $api->get('/hot_search','ArticleController@hot_search');
        //文章列表
        $api->get('/list','ArticleController@list');
        //文章详情
        $api->get('/details','ArticleController@details');
    });
    //预约供货
    $api->group(['prefix'=>'subscribe'],function ($api){
        //车牌地区识别
        $api->get('carDiscern','SubscribeController@carDiscern');
        //车牌字母
        $api->get('carLetter','SubscribeController@carLetter');
        //获取车轴数
        $api->get('axleNumber','SubscribeController@axle_number');
        //获取供货货品
        $api->get('goods','SubscribeController@goods');
        //临时供货
        $api->get('temp','SubscribeController@temp');
        //供货商供货
        $api->get('sup','SubscribeController@supplier')->middleware('authToken');
    });
    //排队货品分类
    $api->get('queue/type','QueueController@queueType');
    //车辆排队
    $api->get('queue','QueueController@queue');
    //获取排队小程序码
    $api->get('queue/getcode','AuthController@getQueueCode');
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
