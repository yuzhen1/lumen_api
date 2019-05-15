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

$router->post('/login/myself', 'LoginController@myself');//个人中心
//$router->post('myself',['middleware'=>['checkLogin'],'LoginController@myself']);
//$router->post('login/myself', ['middleware' => 'checkLogin', function () {
////    return [];
//}]);
$router->group(['middleware' => 'checkLogin'], function () use ($router) {
    $router->post('/myself',['myself'=>'LoginController@myself']);
});
//ajax页面请求接口测试
$router->get('/test/test', 'TestController@test');