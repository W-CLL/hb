<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use app\push\controller\PushEvent;
use app\push\model\Push as PushModel;

class Tenxun extends Controller
{

    //推送api
    function pushApi()
    {
        $token = '106853221585912268'; //广告主线索平台token
        $secret = 'ba6e69d2162621409488dfb6e27d2d0a'; //广告主线索平台生成的接口密钥

        $timestamp = time(); //即当前的秒级时间戳
        $signature = base64_encode($token . "," . $timestamp . "," .
            sha1($token . "." . $timestamp . "." . $secret));

        header("X-Signature: {$signature}");

        // $content = file_get_contents('php://input');
        $input = input();
        file_put_contents('log/tenxunApi.txt', $input['account_id'] . "\r\n", FILE_APPEND);
        // $input = json_decode($content, true);

        // 根据落地页id找到客户,如果没有落地页就不管了
        if ($input['page_id']) {
            $client = Db::table('kw')->where('Kw', 'like', '%' . $input['page_id'] . '%')->where('Client_Id', 'not null')->find();
            if (!$client) {
                // echo Db::getLastSql();
                file_put_contents('log/tenxunApi_error.txt', date('Y-d-m H:i:s') . "该落地页id没有找到对应关键词\r\n" . $input['account_id']  . "\r\n", FILE_APPEND);
                echo '{"code":0,"message": "success","data": [{}]}';
                // header('HTTP/1.1 400 BadRequest');
                exit();
            }
            $data['Client_Id'] = $client['Client_Id'];
            $data['Kw'] = $client['Name'];
            foreach ($input as $k => $v) {
                switch ($k) {
                    case 'leads_name':
                        $data['Content'] .= '姓名 '  . $v . ' |';
                        break;
                    case 'leads_tel':
                        $data['Content'] .= '电话 '  . $v . ' |';
                        break;
                    case 'address':
                        $data['Content'] .= '详细地址 '  . $v . ' |';
                        break;
                    case 'tel_location':
                        $data['Content'] .= '号码归属地 '  . $v . ' |';
                        break;
                    case 'adgroup_name':
                        $data['Content'] .= '广告名称 '  . $v . ' |';
                        break;
                    case 'page_url':
                        $data['Content'] .= '落地页url ' . $v . ' |';
                        break;
                }
            }
            foreach ($input as $v) {
                $data['Content'] .=  $v . ' |';
            }
            $data['User_Id'] = 66;
            $data['Cre_time'] = time();
            $data['Phone'] = $input['leads_tel'];
            $res = Db::table('push')->insert($data);

            if ($res) {
                // 尝试推送
                try {
                    $push = new PushEvent();
                    $push->setUser('400' . $client['Client_Id'] . '009')->setContent('您有新的推送！')->push();
                    $wx_push = new \app\wxapi\controller\Wx();
                    $wx_push->push_clue($client['Client_Id'], array(
                        'first' => '您有新的推送！',
                        'name' => $input['leads_name'],
                        'phone' => $input['leads_tel'],
                        'weixin' => '号码归属地：' . $input['tel_location'],
                        'time' => date('Y-m-d H:i:s'),
                        'remark' => '详细内容可以登录后台查看',
                    ));
                } catch (\Exception $e) {
                    //出错就写入错误文件
                    file_put_contents("PushError.txt", $e, FILE_APPEND);
                }
            }
        } else {
            echo '{"code":0,"message": "success","data": [{}]}';
            exit();
        }
        echo '{"code":0,"message": "success","data": [{}]}';
        exit();
    }

    //拉取api
    function pullApi()
    {
        $token = '106853221585912268'; //广告主线索平台token
        $secret = 'ba6e69d2162621409488dfb6e27d2d0a'; //广告主线索平台生成的接口密钥

        $timestamp = time(); //即当前的秒级时间戳
        $signature = base64_encode($token . "," . $timestamp . "," .
            sha1($token . "." . $timestamp . "." . $secret));

        header("X-Signature: {$signature}");

        //拉取地址
        $url = 'http://leads.qq.com/api/mv1/leads/list?start_time=2020-04-15&end_time=2020-04-22';
        $res = curlOpen($url, array('ssl' => true, 'header' => array("X-Signature: {$signature}")));
        var_dump($res);
    }
}
