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
        if(empty($user_id)){
            $response=[
                'errno'=>'50001',
                'msg'=>'请先登录'
            ];
            return json_encode($response);
        }
        //检测是否选择商品
        if(empty($goods_id)){
            $response=[
                'errno'=>'50011',
                'msg'=>'请至少选择一件宝贝进行结算哦'
            ];
            return json_encode($response);
        }
        //处理goods_id
        $id=explode(',',$goods_id);
        $goodsInfo=DB::table('cart')
            ->join('goods','cart.goods_id','=','goods.goods_id')
            ->where('cart.car_status',1)
            ->whereIn('cart.goods_id',$id)
            ->get();
        $countPrice = 0;
        foreach($goodsInfo as $k=>$v){
            $countPrice += $v->buy_num * $v->goods_price;
        }
        //生成订单号
        $order_no = time() . rand(10000000, 99999999);
        $data = [
            'goods_id'=>$goods_id,
            'order_no'=>$order_no,
            'user_id'=>$user_id,
            'order_amount'=>$countPrice,
            'create_time'=>time()
        ];
        $res = DB::table('order')->insertGetId($data);
        if($res){
            $response=[
                'errno'=>0,
                'msg'=>'创建订单成功'
            ];
            return $response;
        }
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
