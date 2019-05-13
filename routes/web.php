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