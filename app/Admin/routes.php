<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    //供货商
    $router->group(['prefix'=>'suppliers'],function($router){
        //供应商管理
        $router->get('/','SupplierController@list')->name('admin.supList');
        //供货商查看
        $router->get('/{id}','SupplierController@show')->name('admin.supShow');
        //供货商编辑
        $router->get('/{id}/edit','SupplierController@edit')->name('admin.supEdit');
        $router->put('/{id}','SupplierController@update');
        //删除供货商
        $router->delete('/{id}','SupplierController@destroy');
        //审核通过
        $router->post('/{id}/success','SupplierController@success');
        //审核拒绝
        $router->post('/{id}/refuse','SupplierController@refuse');
    });
    //资讯分类
    $router->group(['prefix'=>'articlecategory'],function($router){
        //资讯分类管理
        $router->get('/','ArticleCategoryController@index');
        //新增资讯分类
        $router->get('/create','ArticleCategoryController@create');
        $router->post('/','ArticleCategoryController@store');
        //编辑文章分类
        $router->get('/{id}/edit','ArticleCategoryController@edit');
        //修改文章分类
        $router->put('/{id}','ArticleCategoryController@update');
        //删除文章分类
        $router->delete('/{id}','ArticleCategoryController@destroy');
    });
    //资讯
    $router->group(['prefix'=>'article'],function($router){
        //资讯列表
        $router->get('/','ArticleController@index');
        //新增资讯
        $router->get('/create','ArticleController@create');
        $router->post('/','ArticleController@store');
        //编辑资讯
        $router->get('/{id}/edit','ArticleController@edit');
        $router->put('/{id}','ArticleController@update');
        //删除资讯
        $router->delete('/{id}','ArticleController@destroy');
    });
    //资讯标签
    $router->group(['prefix'=>'articletag'],function($router){
        //资讯标签列表
        $router->get('/','ArticleTagController@index');
        //新增标签
        $router->get('/create','ArticleTagController@create');
        $router->post('/','ArticleTagController@store');
        //编辑标签
        $router->get('/{id}/edit','ArticleTagController@edit');
        $router->put('/{id}','ArticleTagController@update');
        //删除标签
        $router->delete('/{id}','ArticleTagController@destroy');
    });
    //预约供货
    $router->group(['prefix'=>'subscribe'],function ($router){
        //车牌地区识别
        $router->get('carDiscern','CarDiscernController@index');
        //新增车牌地区识别
        $router->get('/carDiscern/create','CarDiscernController@create');
        $router->post('/carDiscern','CarDiscernController@store');
        //编辑地区识别
        $router->get('/carDiscern/{id}/edit','CarDiscernController@edit');
        $router->put('/carDiscern/{id}','CarDiscernController@update');
        //删除地区识别
        $router->delete('/carDiscern/{id}','CarDiscernController@destroy');
        //车牌字母
        $router->get('carLetter','CarletterController@index');
        //新增车牌字母
        $router->get('carLetter/create','CarletterController@create');
        $router->post('carLetter','CarletterController@store');
        //编辑车牌字母
        $router->get('carLetter/{id}/edit','CarletterController@edit');
        $router->put('carLetter/{id}','CarletterController@update');
        //删除车牌字母
        $router->delete('carLetter/{id}','CarletterController@destroy');
        //车轴数管理
        $router->get('axleNumber','AxleNumberController@index');
        //新增车轴数
        $router->get('axleNumber/create','AxleNumberController@create');
        $router->post('axleNumber','AxleNumberController@store');
        //编辑车轴数
        $router->get('axleNumber/{id}/edit','AxleNumberController@edit');
        $router->put('axleNumber/{id}','AxleNumberController@update');
        //删除车轴数
        $router->delete('axleNumber/{id}','AxleNumberController@destroy');
        //预约货品列表
        $router->get('/goods','SubscribeGoodController@index');
        //新增预约货品
        $router->get('/goods/create','SubscribeGoodController@create');
        $router->post('/goods','SubscribeGoodController@store');
        //编辑预约货品
        $router->get('/goods/{id}/edit','SubscribeGoodController@edit');
        $router->put('/goods/{id}','SubscribeGoodController@update');
        //删除预约货品
        $router->delete('goods/{id}','SubscribeGoodController@destroy');
        //供货记录
        $router->get('list','SubscribeSupplyController@index');
        //供货记录查看
        $router->get('list/{id}','SubscribeSupplyController@show');
        //上传供货图片
        $router->get('list/{id}/edit','SubscribeSupplyController@edit');
        $router->put('list/{id}','SubscribeSupplyController@update');
        //删除供货记录
        $router->delete('/list/{id}','SubscribeSupplyController@destroy');
        //预约黑名单
        $router->get('/black/list','SupplyBlackListController@index');
        //新增预约黑名单
        $router->get('black/list/create','SupplyBlackListController@create');
        $router->post('black/list','SupplyBlackListController@store');
        //删除预约黑名单
        $router->delete('/black/list/{id}','SupplyBlackListController@destroy');
        //车牌黑名单
        $router->get('/black/car/list','CarBlackListController@index');
        //新增车牌黑名单
        $router->get('/black/car/list/create','CarBlackListController@create');
        $router->post('/black/car/list','CarBlackListController@store');
        //删除车牌黑名单
        $router->delete('/black/car/list/{id}','CarBlackListController@destroy');
    });
    //系统设置
    $router->get('settings','FormController@setting');
    //清除系统缓存
    $router->get('/clearcache','ClearCacheController@index')->name('admin.clearCache');
    //清除系统配置
    $router->get('/cacheConfig','ClearCacheController@cacheConfig')->name('admin.clearConfig');
});
