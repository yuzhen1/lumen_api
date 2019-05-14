<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
//header("Access-Control-Allow-Origin: http://client.1809a.com");
class TestController extends Controller{
    public function test(){
        echo time();
//        return view('test/test');
    }
}
