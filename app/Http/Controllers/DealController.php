<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class DealController extends Controller
{
    //加密
    const IV = "d89fb057f6d4f03g";//加密向量，16个字节
    const KEY = 'e9c8e878ee8e2658';//密钥，16个字节

    //
    public function index(Request $request){
        $data = file_get_contents('php://input');
        echo $this->decrypt($data);
    }
    //
    public static function decrypt($strEncryptCode,$key = self::KEY,$iv = self::IV)
    {
        $strEncrypted = base64_decode($strEncryptCode);
        return openssl_decrypt($strEncrypted, "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
    }

    //openssl解密
    public function opssl_decode(){
        $data = file_get_contents('php://input');
        $after_str = base64_decode($data);
        $method = 'AES-256-CBC';
        $key = 'abcdefg';
        $options = OPENSSL_RAW_DATA;
        $iv = 'd89fb057f6d44r5z';
        $enc_str = openssl_decrypt($after_str,$method,$key,$options,$iv);
        echo "密文：".$data;echo "<br>";
        echo "原文：".$enc_str;echo "<br>";
//        echo $enc_str;
    }

    //私钥加密  公钥解密
    public function male_decode(){
        $data = file_get_contents('php://input');
        $after_data = base64_decode($data);
//        var_dump($after_data);
        //解密数据
        $public_key = openssl_pkey_get_public('file://'.storage_path('/app/keys/public.pem'));
        openssl_public_decrypt ($after_data,$de_data,$public_key);
        var_dump($de_data);
    }

    //公钥加密  私钥解密
    public function private_decode(){
        $data = file_get_contents('php://input');
        $str = base64_decode($data);
//        echo $str;die;
        //解密
        $pri_key = openssl_get_privatekey('file://'.storage_path('app/keys/private.pem'));
//        var_dump($pri_key);die;
        openssl_private_decrypt($str,$de_data,$pri_key);
        var_dump($de_data);
    }

    //验签
    public function sign_verify(){
        $data = file_get_contents("php://input");
        $sign = $_GET['sign']??'';
        if(empty($sign)){
            die('参数不完整');
        }
        $base_sign = base64_decode($sign);
        $public_key = openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        $aa = openssl_verify($data,$base_sign,$public_key);
        var_dump($aa);
    }
}
