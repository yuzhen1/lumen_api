<?php

namespace App\Http\Controllers\Car;
use App\Http\Controllers\Controller;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class CarController extends Controller{
    //加入购物车
    public function car_add(){
        $str = $_GET;
        $url = "http://pass.verify.zyzyz.top/car/car_add";
        $this->curl($url,$str);
    }

    //购物车列表
    public function car_list(){
        $goodsInfo = DB::table('cart')
            ->join('goods', 'goods.goods_id', '=', 'cart.goods_id')
            ->get();
//        dd($goodsInfo);
        return json_decode($goodsInfo);
    }

    //获取总价格
    public function getAllPrice(){
        $id = $_GET['goods_id'];
        $user_id = $_GET['user_id'];
        $goods_id=explode(',',$id);
        $userWhere=[
            'user_id'=>$user_id
        ];
        $cartInfo = DB::table('cart')->where($userWhere)->whereIn('goods_id',$goods_id)->get();
        $goodsInfo = DB::table('goods')->whereIn('goods_id',$goods_id)->get();
        $countPrice=0;
        foreach ($cartInfo as $k=>$v){
            foreach ($goodsInfo as $key=>$val){
                if($v->goods_id==$val->goods_id){
                    $countPrice += $v->buy_num * $val->goods_price;
                }
            }
        }
        return $countPrice;
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
