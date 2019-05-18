<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/deal', 'DealController@index');
$router->post('/deal/opssl_decode', 'DealController@opssl_decode');//openssl解密
$router->post('/deal/male_decode', 'DealController@male_decode');//私钥加密使用公钥解密
$router->post('/deal/private_decode', 'DealController@private_decode');//公钥加密使用私钥解密
$router->post('/deal/sign_verify', 'DealController@sign_verify'); //验签

//注册登录
$router->post('/login/register', 'LoginController@register');
$router->post('/login/login', 'LoginController@login');
$router->post('/login/login2', 'LoginController@login2');//不加密
//个人中心
$router->group(['middleware' => 'checkLogin'], function () use ($router) {
    $router->get('/login/myself',['uses'=>'LoginController@myself']);
});

//ajax页面请求接口测试
$router->get('/test/test', 'TestController@test');

//商品
$router->get('/goods/goods_list', 'Goods\GoodsController@goods_list');//商品列表
$router->get('/goods/goods_detail', 'Goods\GoodsController@goods_detail');//商品详情

//购物车
$router->get('/car/car_add', 'Car\CarController@car_add');//加入购物车
$router->get('/car/car_list', 'Car\CarController@car_list');//购物车列表
$router->get('/car/getAllPrice', 'Car\CarController@getAllPrice');//获取总价格

//订单
$router->get('/order/create', 'Order\OrderController@create');//
$router->get('/order/order_list', 'Order\OrderController@order_list');//订单列表
