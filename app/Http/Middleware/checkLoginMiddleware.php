<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class checkLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $_POST['token'];
        $user_id = $_POST['user_id'];
        $key = "login_token:user_id:".$user_id;
//        if(empty($token) || empty($user_id)){
//            $response=[
//                'error'=>50032,
//                'msg'=>'参数不完整'
//            ];
//            die(json_encode($response,JSON_UNESCAPED_UNICODE));
//        }
        if(empty($token)){
            $response=[
                'error'=>5032,
                'msg'=>'token不存在'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($user_id)){
            $response=[
                'error'=>50902,
                'msg'=>'id不完整'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }

        if($token){
            $before_token = Redis::get($key);
            if($token!=$before_token){
                $response=[
                    'error'=>50032,
                    'msg'=>'token有误'
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }else{
                //存入日志
                $info = date('Y-m-d H:i:s').'用户id为'.$user_id.'登录成功';
                file_put_contents('logs/login.log',$info,FILE_APPEND);
            }
        }else{
            $response=[
                'error'=>50031,
                'msg'=>'请先登录'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        return $next($request);
    }
}
