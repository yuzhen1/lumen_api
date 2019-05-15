<?php

namespace App\Http\Controllers;

use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class LoginController extends Controller{
    //注册
    public function register(request $request){
        $de_data = $_POST;
        $str = json_encode($de_data,true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pass.verify.com/login/register");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$str);
        $output = curl_exec($ch);
        print_r($output);
        curl_close($ch);
    }

    //登录
    public function login(){
        $data = file_get_contents('php://input');
        $data2 = base64_decode($data);
        $public_key = openssl_pkey_get_public('file://'.storage_path('/app/keys/public.pem'));
        openssl_public_decrypt($data2,$de_string,$public_key);
        $de_data = json_decode($de_string,true);
        //验证邮箱
        $u = DB::table('user')->where(['user_email'=>$de_data['user_email']])->first();

        if($u){
            //存在  验证密码
            if(password_verify($de_data['password'],$u->password)){
                $token = $this->getLoginToken($u->user_id);
                $key = "login_token:user_id:".$u->user_id;
                Redis::set($key,$token);
//                $get = Redis::get($key);//取出存储的值
//                var_dump($get);die;
                Redis::expire($key,604800);
                //生成token
                $response=[
                    'errno'=>0,
                    'msg'=>'登录成功',
                    'data'=>[
                        'token'=>$token
                    ]
                ];

            }else{
                //登录失败
                $response=[
                    'errno'=>50003,
                    'msg'=>'密码不正确,请重新登录'
                ];
            }
        }else{
            $response=[
                'errno'=>50002,
                'msg'=>'该用户不存在,请重新登录'
            ];
        }
        die(json_encode($response,JSON_UNESCAPED_UNICODE));
    }

    //登录  不加密
    public function login2(){
        $data = $_POST;
        $str = json_encode($data,true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pass.verify.com/login/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$str);
        $output = curl_exec($ch);
        print_r($output);
        curl_close($ch);
    }

    //获取登录token
    public function getLoginToken($user_id){
        $rand_str = Str::random(10);
        $token = substr(md5($user_id.time().$rand_str),5,15);
        return $token;
    }

    //个人中心
    public function myself(){
        $user_id = $_GET['user_id'];
//        echo $user_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pass.verify.com/login/myself");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$user_id);
        $output = curl_exec($ch);
        print_r($output);
        curl_close($ch);
    }

}
