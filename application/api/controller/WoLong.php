<?php

namespace app\api\controller;

use phpDocumentor\Reflection\Types\Object_;
use think\Controller;
use think\Db;
use think\Validate;


class WoLong extends Controller
{
    protected $source;
    protected $url;

    public function __construct()
    {
        // $this->url = $url;
    }

    /**
     * 检查落地页中是否含有clickid,有的话就尝试转化，没有的话返回false
     */
    static public function conv($url)
    {
        $array = getParams($url);
        if (empty($array['clickid'])) {
            return false;
        }
        //转化类型 1激活 2下载 3浏览关键词页面 5表单提交 6拨打电话 10提交订单 11购物 12注册 13在线咨询 14其他 15访客数
        $user = ['username' => '北渠515', 'password' => 'ZSLD2018'];
        $data = ['click_id' => $array['clickid'], 'conv_type' => '5', 'conv_name' => '测试2', 'conv_value' => '1'];
        //回传api
        $conv_res = \app\api\controller\WoLong::conversion1($user, $data);
        if ($conv_res) {
            return true;
        }
        return false;
    }

    /**回传api，一条 ，需要的参数如下
     * 
     * $user = ['username' => '', 'password' => ''];卧龙推广的账户名和密码
     * 
     * $data = ['click_id' => '点击ID', 'conv_type' => '转化类型', 'conv_name' => '转化名称', 'conv_value' => '转化值'];
     */
    static public function conversion1($user = array(), $data = array())
    {
        header('Content-Type:application/json; charset=utf-8');
        $url = "https://e.sm.cn/api/uploadConversions";

        $post = array(
            "header" => [
                'username' => $user['username'],
                "password" => $user['password'],
                // 'token' => "65d9411c-32a8-4da7-834e-afafd8acf730"
            ],
            "body" => [
                "source" => 0, //数据来源 0广告主 1第三方数据 3第三方工具
                "data" => [
                    [
                        "date" => date("Y-m-d", time()), //当前转化记录的日期
                        "click_id" => $data['click_id'], //点击ID
                        "conv_type" => $data['conv_type'], //转化类型
                        "conv_name" => $data['conv_name'], //转化名称
                        "conv_value" => $data['conv_value'] //转化值
                    ]
                ]
            ]
        );
        $post = json_encode($post);
        //发送请求
        $res = curlOpen(
            $url,
            array('ssl' => true, "post" => $post, "header" => array('Content-Type:application/json; charset=utf-8'))
        );

        if (!$res) {
            file_put_contents('./log/convError.txt', 'curl请求发送失败' . "\n\r", FILE_APPEND);
            return false;
        }
        $resSta = json_decode($res);
        // 返回的状态为0说明请求成功
        if (!$resSta->header->status == 0) {
            file_put_contents('./log/convError.txt', $res . "\n\r", FILE_APPEND);
            // exit('error');
            return false;
        }
        return true;
    }

    public function ocpc()
    {
        if (request()->isPost()) {
            $data = input('datas');
            // header('Content-Type:application/json; charset=utf-8');
            $url = "https://e.sm.cn/api/uploadConversions";
            //发送请求
            $res = curlOpen(
                $url,
                array('ssl' => true, "post" => $data, "header" => array('Content-Type:application/json; charset=utf-8'))
            );
            return $res ? $res : '请求失败';
        }
        return view();
    }


    // 回传api(激活)
    public function conversion()
    {
        // http://s.ykhwzx.cn/api/WoLong/conversion?idfa={IDFA_SUM}&imei={IMEI_SUM}&time={TS}&callback_url={CALLBACK_URL}&user_id={USERID}&click_id={CLICKID}
        //iso设备标识
        $idfa = input('idfa');
        //安卓设备标识
        $imei = input('imei');
        // 点击时间
        $time = input('time');
        //点击ID
        $click_id =  input('click_id');
        //转化类型 1激活 2下载 3浏览关键词页面 5表单提交 6拨打电话 10提交订单 11购物 12注册 13在线咨询 14其他 15访客数
        // $conv_type = input('conv_type');//这个包含在callback_url中
        $url = input('callback_url'); //回调url

        //转化值，数值
        $conv_value = 1;

        //以上参数用来判断是否转换

        $res = curlOpen(
            $url,
            array('ssl' => true)
        );

        if (!$res) {
            exit(false);
        }
        // var_dump($res);
        $resSta = json_decode($res);
        if ($resSta->header->status == 0) {
            exit(true);
        } else {
            file_put_contents('./log/WoLongError.txt', $res . "\n\r", FILE_APPEND);
            exit('error');
        }
    }
}
