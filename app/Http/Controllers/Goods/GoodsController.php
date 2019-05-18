<?php

namespace App\Http\Controllers\Goods;
use App\Http\Controllers\Controller;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class GoodsController extends Controller{
    //商品列表
    public function goods_list(){
//        echo 111;die;
        $data = DB::table('goods')->get();
        return json_encode($data);
    }

    //商品详情
    public function goods_detail(){
        $goods_id = $_GET['goods_id'];
        $where = [
            'goods_id'=>$goods_id
        ];
        $data = DB::table('goods')->where($where)->first();
        if($data){
            $data = [
                'errno'=>'0',
                'data'=>$data
            ];
            return json_encode($data);
        }
    }
}
