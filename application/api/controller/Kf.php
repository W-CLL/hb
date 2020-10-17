<?php

namespace app\api\controller;

use think\Controller;

use think\Db;


class Kf extends Controller
{
    //和53客服对接，识别电话
    public function kf_moblie()
    {
        //对接token跟返回值
        $array = ['cmd' => 'OK', 'token' => 'c375d1326103511096a20abac817859c'];
        
        //接受内容
        $content = input('content');
        $cmd = input('cmd');
        try {
            //解码
            $content = urldecode($content);
            $res = json_decode($content, true);

            //根据命令判断执行
            switch ($cmd) {
                case 'talk_info':
                    file_put_contents("log/53kh_info.txt", $content, FILE_APPEND);
                    $this->save_talk_info($res);
                    break;
                case 'customer':
                    $this->save_kh_info($res);
                    $this->save_kf_moblie($res);
                    break;
            }
        } catch (\Exception $e) {
            //出错就写入错误文件
            file_put_contents("log/error53kf.txt", $e, FILE_APPEND);
            echo json_encode($array);
            die();
        }
        echo json_encode($array);
    }

    /**
     * 检查落地页中是否含有clickid,有的话就尝试转化，没有的话返回false
     */
    private function conv($url)
    {
        $array = getParams($url);
        if (empty($array['clickid']) || empty($array['conv'])) {
            return false;
        }
        $user = ['username' => '安渠566', 'password' => 'ZSLD2020'];
        $data = ['click_id' => $array['clickid'], 'conv_type' => '13', 'conv_name' => '车都市', 'conv_value' => '1'];
        //回传api
        $conv_res = \app\api\controller\WoLong::conversion1($user, $data);
        $conv_res = 1;
        if ($conv_res) {
            return true;
        }
        return false;
    }

    public function test()
    {
        // $str = '{"cmd":"customer","guest_name":"10846858390015","email":"","province":"","city":"","qq":"","mobile":"15157903715","address":"","remark":"","weixin":"","zipcode":"","worker_id":"294350336@qq.com","tag":"","tag_id":"","time":"1586944825","company_id":"72210286","number":"1","guest_id":"10846858390015","id6d":"10351857","talk_page":"http://afnb.sj-fx.com/kffp04@sohu.com/KDJ2/kfc1-xl-kdj-wap/?hgys","se":"手机搜狗","kw":"测试","from_page":"https://wap.sogou.com/bill_cpc?v=1&p=WJedUUVr919UNmap2pGq4g$ydqwSIYN7DLBXGEnElz6kwXryA8DkmDOu1i0mJI5USjMejlq5Tky5L$nkkPiU7xQISvdQS4oICI2Ye92sDvjwna74sY08BHEZKK5WuM@20V1OfLP7NytNm40oLINbX@3F$iM5F21Nak1S0xYWRT5LcHnHUgbucdNS@78hd6@@Z7GTred6Z11HTHvmFaeD@MFqDoMq0bQTfddgK0C@dM8ReHojnAh0WHy8QI9gpexcKoTEZkojJXAqT6lXZLcTNosVv0RXFtHBwMP5MCprLlhjp3ZBpYQdVJDENNBUI9M2@AQ0QB1CaOKAYMe1Qx@wuM0SXtgELMkLh093oHjwOdjarWsK0HdRm@ytSPLvYqFK0CVwDOSZIZFl4415SsG91QFTjF$sl0nPFwkIrGg80v$BIwiqxYM3$PjxZi6RhWj1e9LkISa0vU$dveRon7f1uMvKEWs0p5qJ8kypV6jjxX@SjBZJf7vpD$e1xMitmPJF7Mcn@xFFOxMcAvmT3A$1T543bUdiKS@QmraQJO6o3258QJnhlY6ANqa8@vEJt$qUflNFLjRi9$WUt17wXzvUFElIPpGh6dOnU$lgS8wItxSrXqAdZsDMFMXgz0UB9pfXlkIG5BxBG3OalNZ@qEXUHAZmalm9vSCuUhEl3zkyKUaONZJJeALbUBsBJHA$0bCcNEsI9c13oaDxic@9sk==&q=WJZNhm13ZAlYPmVnisuimzLOaPAPctc0shI$sJwafWe0@rmimx@bOs3B7gJabzYEKqH43hsx7shML3WK38YrLNS11brlXSP05lrmWZ8DVgfV5XfpnUVKwF7YjFOlaqUGH2P0Q$xqVYqJEw@A6RTGj4TclMJuJ5D=&keyword=%E8%82%AF%E5%BE%B7%E5%9F%BA%E5%8A%A0%E7%9B%9F%E6%9D%A1%E4%BB%B6%E5%92%8C%E8%B4%B9%E7%94%A8&ml=0&mc=13&ma=73,0,137,298,137,298,360,615","land_page":"http://afnb.sj-fx.com/kffp04@sohu.com/KDJ2/kfc1-xl-kdj-wap/?clickid=123456&conv=123","guest_ip_info":"浙江省金华市[移动]","device":"2","field_type":"add","field":"mobile","add_field":"mobile","del_field":"","guest_ip":"112.12.65.116","wechat":"","token":"c375d1326103511096a20abac817859c","type":"add"}';
        //  $str = json_encode($str);
        $input = file_get_contents('php://input');
        $input = urlencode($input);
        $input = '{
            "content": "' . $input . '",
            "cmd": "talk_info",
            "token": "c375d1326103511096a20abac817859c"
        }';
        // echo date('YmdHis');
        // echo date('Y-m-d H:i:s',strtotime('20200514161418'));
        echo $input;
    }

    /**
     * 保存单个电话信息
     */
    private function save_kf_moblie($res)
    {
        if (strlen($res['mobile']) > 1) {
            $data['Moblie'] = $res['mobile'];
            $data['Time'] = time();
            //验证电话号，不需要重复数据！！！
            $validate = new \think\Validate(['Moblie|电话号码' => 'require|max:11|number|unique:kf_moblie']);
            if (!$validate->check($data)) {
                //未验证通过就写入错误文件   
                file_put_contents("log/error53kf.txt", $data['Moblie'] . $validate->getError() . date('Y-m-d H:i:s') . "\r\n", FILE_APPEND);
            } else {
                //通过才写入数据库    
                $res_mob = Db::table('kf_moblie')->insert($data);
            }
        }
    }


    /**
     * 保存客户所有信息
     */
    private function save_kh_info($data)
    {
        // 不要重复号码
        // $validate = new \think\Validate(['mobile|访客手机' => 'require']);
        // if (!$validate->check($data)) {
        //     //未验证通过就写入错误文件
        //     file_put_contents("log/error53kf.txt", $validate->getError() . date('Y-m-d H:i:s') . "\r\n", FILE_APPEND);
        //     return false;
        // }

        $where = ['guest_id' => $data['guest_id']];
        $check = Db::table('kh_info')->where($where)->find();
        //有记录就更新,没有就新增
        if ($check) {
            //去掉为空的数据，防止覆盖
            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }
            $res = Db::table('kh_info')->strict(false)->where('Id', $check['Id'])->update($data);
        } else {
            $res = Db::table('kh_info')->strict(false)->insert($data);
        }

        // switch ($data['type']) {
        //     case 'add':
        //         $res = Db::table('kh_info')->strict(false)->insert($data);
        //         break;
        //     case 'edit':
        //         $res = Db::table('kh_info')->strict(false)->where($where)->update($data);
        //         break;
        //     default:
        //         $res = false;
        // }

        return $res ? true : false;
    }


    /**
     * 保存谈话信息
     */
        /**
     * 保存谈话信息
     */
    private function save_talk_info($data)
    {
        //合并数组
        $kh_talk_data = array_merge($data['session'], $data['end']);

        //如果访客有说话也添加下记录
        $kh_talk_data['isTalk'] = '0';
        foreach ($data['message'] as $k => $v) {
            if ($v['msg_type'] == 'g') {
                $kh_talk_data['isTalk'] = '1';
            }
        }

        $res = Db::table('kh_talk')
            ->where('talk_id', $kh_talk_data['talk_id'])
            ->field('Id,talk_id,end_time')
            ->find();
        //如果有结果就更新，没有就插入
        if (!$res) {
            //strict 关闭字段严格检查
            $result = Db::table('kh_talk')->strict(false)->insert($kh_talk_data);
        } else {
            $result = Db::table('kh_talk')->where('Id', $res['Id'])->strict(false)->update($kh_talk_data);
        }
        //保存对话消息修改一下，对话消息不保存到数据库了，保存到文件中,RUNTIME_PATH.talk_message/年月/日/talk_id
        $dir = RUNTIME_PATH . 'talk_message' . DS . date('Ym') . DS . date('d') . DS;
        if (!is_dir($dir)) {
            //如果目录不存在就创建文件夹，并设置权限
            mkdir($dir, 0777, true);
        }
        file_put_contents($dir . $kh_talk_data['talk_id'].'.json', json_encode($data['message']));
        return $result ? true : false;
    }
    // private function save_talk_info($data)
    // {
    //     // $data['session'];
    //     // $data['end'];
    //     // $data['message'];
    //     //合并数组
    //     $kh_talk_data = array_merge($data['session'], $data['end']);
    //     // $model = new \app\kh_info\model\KhTalk;

    //     //如果访客有说话也添加下记录
    //     $kh_talk_data['isTalk'] = '0';
    //     foreach ($data['message'] as $k => $v) {
    //         if ($v['msg_type'] == 'g') {
    //             $kh_talk_data['isTalk'] = '1';
    //         }
    //     }
    //     $res = Db::table('kh_talk')
    //         ->where('talk_id', $kh_talk_data['talk_id'])
    //         ->field('Id,talk_id,end_time')
    //         ->find();

    //     //如果有结果就更新，没有就插入
    //     if (!$res) {
    //         //strict 关闭字段严格检查
    //         $result = Db::table('kh_talk')->strict(false)->insert($kh_talk_data);
    //         $result = Db::table('kh_message')->strict(false)->insertAll($data['message']);
    //     } else {
    //         //如果新消息发送时间小于上次对话的结束时间就是旧消息，不需要
    //         foreach ($data['message'] as $k => $v) {
    //             if ($v['msg_time'] <= $res['end_time']) {
    //                 continue;
    //             }
    //             $message[$k] = $v;
    //         }
    //         $result = Db::table('kh_talk')->where('Id', $res['Id'])->strict(false)->update($kh_talk_data);
    //         if (!empty($message)) {
    //             $result = Db::table('kh_message')->strict(false)->insertAll($message);
    //         }
    //     }
    //     return $result ? true : false;
    // }
}
