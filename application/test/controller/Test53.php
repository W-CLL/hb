<?php

namespace app\test\controller;

use app\common\controller\Common;
use Collator;
use think\Controller;
use think\Db;

class Test53 extends Common
{

    public function test()
    {
        $str = '{
            "content": {
                "session": "{\"talk_id\":1003564742,\"company_id\":72000116,\"id6d\":10000231,\"guest_id\":1003564642,\"guest_ip\":\"125.119.244.209\",\"guest_area\":\" 浙江省杭州市[电信]\",\"referer\":\"https://www.haosou.com/s?ie=utf-8&shb=1& src=360sou_newhome&q=%E5%A9%9A%E7%BA%B1%E6%91%84%E5%BD %B1\",\"talk_page\":\"\",\"se\":\"360\",\"kw\":\"婚纱摄影\",\"talk_time\":\"2015-08-27 16:00:35\",\"land_page\":\"\",\"style_id\":\"12345\",\"style_name\":\"db\",\"talk_type\":\"5\",\"device\":\"1\",\"worker_id\":\"\",\"worker_name\":\"\"}",
                "end": "{\"talk_id\":1003564742,\"company_id\":72000116,\"end_time\":\"2015-09-08 16:19:50\",\"end_type\":2}",
                "message": [
                    "{\"talk_id\":1003564742,\"company_id\":72000116,\"id6d\":10000231,\"msg_time\":\"2015-08-27 16:00:35\",\"msg_type\":\"p\",\"msg\":\"您好，欢迎您的咨询，请问有什么需要帮助的吗？\",\"worker_id\":\"\",\"worker_name\":\"\"}",
                    "{\"talk_id\":1003564742,\"company_id\":72000116,\"id6d\":10000231,\"msg_time\":\"2015-08-27 16:00:35\",\"msg_type\":\"p\",\"msg\":\"您好，欢迎您的咨询，请问有什么需要帮助的吗？\",\"worker_id\":\"\",\"worker_name\":\"\"}"
                ]
            },
            "msg_id": "da9b1ca7-1400-477f-b5ef-2259bbb4077e",
            "cmd": "talk_info",
            "token": "qweasdzxc"
        }';
        // $str = preg_replace("", '"', $str);
        $str = json_decode($str);

        var_dump($str);
        // var_dump($str);
    }
}
