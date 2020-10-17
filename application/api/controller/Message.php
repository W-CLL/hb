<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Validate;
use think\Loader;
use app\push\controller\PushEvent;
use think\Cache;
use think\Cookie;


class Message extends Controller
{
    public function demo()
    {
        // $data = input();
        // var_dump($data);

        // leibie: 
        // froms: 
        // keywords: 
        // xingming: test
        // dianhua: 
        // email: 
        // laizi: 
        header('Content-Tpye:application/json');
        $pass = 1;
        if ($pass) {
            echo json_encode(['code' => 0, 'msg' => '提交成功']);
        } else {
            echo json_encode(['code' => 1, 'msg' => '提交失败，请检查输入内容']);
        }
    }
}
