<?php

namespace App\Http\Controllers;

use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
//header("Access-Control-Allow-Origin: *");
//header('Access-Control-Allow-Methods:OPTIONS,GET,POST');
//header('Access-Control-Allow-Headers:x-requested-with');
class LoginController extends Controller{
    //注册
    public function register(request $request){
//        $data = file_get_contents('php://input');
//        $data2 = base64_decode($data);
//        $public_key = openssl_pkey_get_public('file://'.storage_path('/app/keys/public.pem'));
//        openssl_public_decrypt($data2,$de_string,$public_key);
//        $de_data = json_decode($de_string,true);
//        dd($de_data);
        $de_data = $_POST;
        //验证邮箱
        $user_email = DB::table('user')->where(['user_email'=>$de_data['user_email']])->first();
        if($user_email){
            $response=[
                'errno'=>'50010',
                'msg'=>'该邮箱已被注册'
            ];
           return $response;
        };
//        $password=password_hash($de_data['password'],PASSWORD_BCRYPT);
        $data = [
            'user_name'=>$de_data['user_name'],
            'user_email'=>$de_data['user_email'],
            'password'=>$de_data['password'],
            'add_time'=>time(),
        ];
        //入库
        $res = UserModel::insertGetId($data);
        if($res){
            $response=[
                'errno'=>'0',
                'msg'=>'注册成功,即将跳转至登录页面'
            ];
            return $response;
        }
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
        $send_email = $data['user_email'];
        //验证邮箱
        $res = DB::table('user')->where(['user_email'=>$send_email])->first();
//        var_dump($res);die;
        if($res){
            //验证密码
            if($data['password']==$res->password){
                $token =  $this->getLoginToken($res->user_id);//生成token
                $key = "login_token:user_id:".$res->user_id;
                Redis::set($key,$token);                        //存token
                Redis::expire($key,604800);
//                dd(Redis::get($key));//463f5c2331872b0
                $response=[
                    'errno'=>0,
                    'msg'=>'登录成功',
                    'data'=>[
                        'token'=>$token,
                        'user_id'=>$res->user_id
                    ]
                ];
//                dd($response);
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response=[
                    'errno'=>50003,
                    'msg'=>'密码错误'
                ];
                return $response;
            }
        }else{
            $response=[
                'errno'=>50002,
                'msg'=>'该用户不存在,请重新登录'
            ];
            return $response;
        }
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
       $where = [
           'user_id'=>$user_id
       ];
       $obj = DB::table('user')->where($where)->first();
       if($obj){
           $response = [
               'errno'=>'0',
               'msg'=>"success",
               'user_name'=>$obj->user_name,
               'user_email'=>$obj->user_email
           ];
           return $response;
       }else{
           $response = [
               'errno'=>'50001',
               'msg'=>'请登陆'
           ];
           return $response;
       }
    }

}
