<?php

namespace App\Http\Controllers\Order;
use App\Http\Controllers\Controller;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class OrderController extends Controller{
    //创建订单
    public function create(){
        $goods_id = $_GET['goods_id'];
        $user_id = $_GET['user_id'];
        $str = [
            'goods_id'=>$goods_id,
            'user_id'=>$user_id
        ];
        $url = "http://pass.verify.zyzyz.top/car/car_add";
        $this->curl($url,$str);
    }

    public function order_list(){
        $user_id = $_GET['user_id'];
        $orderInfo =DB::table('order')
            ->join('goods','order.goods_id','=','goods.goods_id')
            ->where(['user_id'=>$user_id])
            ->get();
        return json_encode($orderInfo);
    }

    //CURL
    public function curl($url,$str){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$str);
        $output = curl_exec($ch);
        print_r($output);
        curl_close($ch);
    }
}
