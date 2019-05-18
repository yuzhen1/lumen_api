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
        $data = $_GET;
//       验证
        if($data->buy_num==0){
            $response=[
                'errno'=>'40003',
                'msg'=>'请选择购买数量'
            ];
            return $response;
        }
        if(empty($data['user_id'])){
            $response=[
                'errno'=>'40004',
                'msg'=>'请先登录'
            ];
            return $response;
        }


        //加入购物车
        if($data['goods_id']){
            //查询数据库中是否有当前用户的商品数据
            $where=[
                'goods_id'=>$data['goods_id']
            ];
            $car_status = DB::table('cart')->where($where)->first();
            if($car_status['is_up']==2){
                $response=[
                    'errno'=>'50012',
                    'msg'=>'该商品已下架'
                ];
                return $response;
            }
            if($car_status){
                //修改
                $update_num=[
                    'buy_num'=>$car_status->buy_num+$data['buy_num']
                ];
                $res = DB::table('cart')->where($where)->update($update_num);
                if($res){
                    $arr=[
                        'errno'=>0,
                        'msg'=>'加入购物车成功'
                    ];
                }else{
                    $arr=[
                        'errno'=>'50010',
                        'msg'=>'加入购物车失败'
                    ];
                }
                echo json_encode($arr);

            }else{
                //增加
                $info=[
                    'goods_id'=>$data['goods_id'],
                    'goods_price'=>$data['goods_price'],
                    'user_id'=>$data['user_id'],
                    'buy_num'=>$data['buy_num'],
                    'created_at'=>time(),
                    'updated_at'=>time()
                ];
                $res = DB::table('cart')->insertGetId($info);
                if($res){
                    $response=[
                        'errno'=>0,
                        'msg'=>'加入购物车成功'
                    ];
                    return $response;
                }
            }
        }else{
            $response=[
                'errno'=>'40005',
                'msg'=>'请进行正确的操作'
            ];
            return $response;
        }
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


}
