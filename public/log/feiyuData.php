<?php
class Feiyu
{
    public function __construct()
    {
        $link = mysqli_connect('127.0.0.1', 'www_hb_demo', 'abc', 'www_hb_demo');
        // 检测连接是否成功
        if ($link->connect_error) {     //如果没有错误,会返回一个NULL
            file_put_contents('./log/feiyu.txt', date('Y-m-d H:i:s') . " 连接失败，错误:" . $link->connect_error . "\n", FILE_APPEND);
            die();
        }
        $this->pull($link);
        $link->close();
    }
    //这里开一个接口让Linux后台每隔10分钟执行调用这个接口一次
    public function pull($link)
    {
        // file_put_contents('./log/feiyu.txt', date('Y-m-d H:i:s') . "调用\n", FILE_APPEND);
        //默认获取今天的数据
        $input['startDate'] = date('Y-m-d');
        $input['endDate'] = date('Y-m-d', strtotime('+1 day'));
        $input['page'] = 1;
        $input['page_size'] = 100;

        // 从数据库中获取需要的key和token,发送请求,得到数据，保存到一个数组中
        $sql = "select * from feiyu_user;";
        $res = $link->query($sql);
        if (!$res) {
            die();
        }
        $data = [];
        $v = $res->fetch_assoc();
        while ($v) {
            // var_dump($v);
            //如果状态为0,跳过拉取
            if ($v['status'] == '0') {
                file_put_contents('./log/feiyu.txt', date('Y-m-d H:i:s') . "跳过\n", FILE_APPEND);
                $v = $res->fetch_assoc();
                continue;
            }
            $input['key'] = $v['Key'];
            $input['token'] = $v['Token'];
            $d = $this->getData($input);
            if (!empty($d['data'])) {
                //每一条线索都有一个属于他的客户
                foreach ($d['data'] as $k2 => $v2) {
                    //这里本来应该用$d['data'][$k2]['client_id'] = $v['Client_Id']的，但是一个客户会对应几个账号，于是就改了;
                    $d['data'][$k2]['client_id'] = $v['Id'];
                    $d['data'][$k2]['user_id'] = $v['Client_Id'];
                }
                $data = array_merge($data, $d['data']);
            }
            //根据总线索数量,计算页数,如果页数大于1，那么就循环页数调用获取线索数据
            $num = ceil($d['count'] / $input['page_size']);
            if ($num >= 2) {
                //根据页数调用数据
                for ($i = 2; $i <= $num; $i++) {
                    $input['page'] = $i;
                    $d = $this->getData($input);
                    if (!empty($d['data'])) {
                        $data = array_merge($data, $d['data']);
                    }
                }
            }
            $input['page'] = 1; //全部调用完成之后记得页数回归1
            $v = $res->fetch_assoc();
        }

        foreach ($data as $k => $v) {
            //保存每一个clue_id，方便过滤数据
            $clue_ids[] = $v['clue_id'];
            //将键值对反过来保存，这么做是为了一次循环就过滤数据
            $clue_id = $v['clue_id'];
            $ids[$clue_id] = $k;
        }

        //过滤数据
        $sql2 = 'select Id,clue_id from feiyu_clues where clue_id in (' . implode(',', $clue_ids) . ');';
        $res2 = $link->query($sql2);

        $v2 = $res2->fetch_assoc();
        while ($v2) {
            $clue_id = $v2['clue_id'];
            if (isset($ids[$clue_id])) {
                unset($data[$ids[$clue_id]]);
            }
            $v2 = $res2->fetch_assoc();
        }

        //过滤后如果不剩数据就退出
        if (empty($data)) {
            // return false;
            echo '数据为空';
            die();
        }

        //过滤字段
        $n = 0;
        foreach ($data as $k => $v) {
            $save[$n]['adv_name'] = $v['adv_name'];
            $save[$n]['clue_id'] = $v['clue_id'];
            $save[$n]['clue_type'] = $v['clue_type'];
            $save[$n]['external_url'] = $v['external_url'];
            $save[$n]['create_time'] = $v['create_time'];
            $save[$n]['clue_source'] = $v['clue_source'];
            $save[$n]['ad_plan_name'] = $v['ad_plan_name'];
            // $save[$n]['date'] = $v['date'];
            $save[$n]['convert_status'] = $v['convert_status'];
            $save[$n]['appname'] = $v['appname'];
            $save[$n]['module_id'] = $v['module_id'];
            $save[$n]['module_name'] = $v['module_name'];
            $save[$n]['name'] = $v['name'];
            $save[$n]['telphone'] = $v['telphone'];
            $save[$n]['location'] = $v['location'];
            $save[$n]['address'] = $v['address'];
            $save[$n]['client_id'] = $v['client_id'];
            //用于查找用户推送消息提醒的数组，为了去重，键名也设置
            $user_ids[$v['user_id']] = $v['user_id'];

            //保存到数据库中的数据
            $values[$n] = '(\'' . implode('\',\'', $save[$n]) . '\')';
            $n += 1;
        }
        // 过滤后执行保存
        // $result = Db::table('feiyu_clues')->insertAll($save);
        $sql3 = 'insert into feiyu_clues(adv_name,clue_id,clue_type,external_url,create_time,clue_source,ad_plan_name,convert_status,
                                        appname,module_id,module_name,name,telphone,location,address,client_id)
                 values ' . implode(',', $values) . ';';

        $link->query("set names 'utf8'");
        $result = $link->query($sql3);

        if (!$result) {
            file_put_contents('./log/feiyu.txt', date('Y-m-d H:i:s') . '保存数据错误' . "\n", FILE_APPEND);
        }
    }

    /**
     * 发送请求，返回['data' => '数据', 'count' => '线索总数']
     *
     * @return array|bool
     */
    protected function getData($input)
    {
        // 发送数据请求
        // $json = $this->pullclues($input);
        $json = $this->test($input);

        // 解析数据
        $json = json_decode($json, true);

        //状态-2标识拉取失败
        if ($json['status'] == '-2') {
            file_put_contents('./log/feiyu.txt', date('Y-m-d H:i:s') . $json['msg'] . "\n", FILE_APPEND);
            return false;
        }

        return ['data' => $json['data'], 'count' => $json['count']];
    }

    /**
     * 这里接收请求参数，调用飞鱼api拉取数据，成功返回json字符串 ，失败返回false
     */
    public function pullclues($input)
    {

        // 今日头条飞鱼CRM系统API域名
        $host = 'https://feiyu.oceanengine.com';

        // 拉取数据的路由
        $pull_route = '/crm/v2/openapi/pull-clues/';

        // 飞鱼CRM系统中,你生成的秘钥字符串,这里要替换成你自己的key
        $signature_key = $input['key'];

        // 飞鱼CRM系统中,你生成的Token字符串,这里要替换成你自己的Token
        $token = $input['token'];

        // 飞鱼的加密参数还有三个:start_time、end_time、timestamp,这三个字段都是时间戳格式

        // 拉取的数据从哪一天开始取数据,开始日期,例如从 2019-08-01 开始取数据
        $start_time = strtotime($input['startDate']);

        // 拉取的数据到哪一天截止停止取数据,结束日期,例如到 2019-09-01 停止取数据
        $end_time = strtotime($input['endDate']);

        // 时间戳,当前执行加密方法的时间
        $timestamp = time();

        // 飞鱼CRM担心数据量太大会挂掉,所以要求进行分页获取数据,这个是第几页
        $page = $input['page'];

        // 每页数据要多少条,例如我设置每页获取10条数据
        $page_size = $input['page_size'];

        // 以上必须得参数都提供完整无误后,就可以进行数据加密了

        // 第一步,将拉取数据的路由和开始日期和结束日期和时间戳进行拼接,结果类似这样:"/crm/v2/openapi/pull-clues/?start_time=1569859200&end_time=1572537600 1572574424"
        // 注意:这一步有一个空格,一定要保留,否则签名会失败的!!!
        $data = $pull_route . '?start_time=' . $start_time . '&end_time=' . $end_time . ' ' . $timestamp;

        // 第二步,将第一步拼接后的字符串进行哈希256加密,然后将结果再进行base64加密
        $signature = base64_encode(hash_hmac('sha256', $data, $signature_key));

        // 第三步,配置curl信息,然后获取数据,请求地址类似这样:"https://feiyu.oceanengine.com/crm/v2/openapi/pull-clues/?page=1&page_size=10&start_time=1569859200&end_time=1572537600"
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host . $pull_route . '?page=' . $page . '&page_size=' . $page_size . '&start_time=' . $start_time . '&end_time=' . $end_time);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        // 设置请求头信息,每个头信息的冒号后面要保留一个空格
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=UTF-8',
            'Signature: ' . $signature,
            'Timestamp: ' . $timestamp,
            'Access-Token: ' . $token,
        ]);
        // ssl证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 执行curl获取数据
        $output = curl_exec($ch);

        // 关闭资源
        curl_close($ch);

        // 如果不出意外,那么这里你就可以获取到飞鱼CRM系统中的客户信息了
        return $output;
    }
    //测试数据
    public function test($input)
    {
        return '{
                "status": "success",
                "count": 2,
                "data": [
                    {
                        "adv_id": "1664750332606472",
                        "store_id": 0,
                        "clue_id": "6831434361875726350",
                        "clue_source": 1,
                        "weixin": "",
                        "adv_name": "\u62db\u76df\u7f51\u7edczhuyoushang8120255@163.com",
                        "site_id": null,
                        "convert_status": "\u5408\u6cd5\u8f6c\u5316",
                        "date": null,
                        "create_time": "1590567259",
                        "remark_dict": {},
                        "address": "-",
                        "city_name": "",
                        "province_name": "",
                        "module_id": "1667193548958733",
                        "ad_plan_id": "1667816235431981",
                        "form_remark": "",
                        "qq": "",
                        "remark": "",
                        "ad_plan_name": "oCPM_05_27",
                        "name": "\u672a\u547d\u540d",
                        "appname": "\u6296\u97f3",
                        "gender": 0,
                        "age": 0,
                        "cid": 0,
                        "telphone": "13775502308",
                        "store_name": "",
                        "location": "\u6c5f\u82cf+\u9547\u6c5f",
                        "req_id": "9223372036854775807",
                        "clue_type": 1,
                        "module_name": "\u718a\u672c\u718a53",
                        "external_url": "https://www.chengzijianzhan.com/tetris/page/6828807535098462221/",
                        "email": "",
                        "store": {
                            "store_name": "",
                            "store_id": 0,
                            "store_pack_remark": "",
                            "store_pack_name": "",
                            "store_remark": "",
                            "store_address": "",
                            "store_location": "",
                            "store_pack_id": 0
                        }
                    },
                    {
                        "adv_id": "1664750332606472",
                        "store_id": 0,
                        "clue_source": 1,
                        "weixin": "",
                        "adv_name": "\u62db\u76df\u7f51\u7edczhuyoushang8120255@163.com",
                        "site_id": "6828807535098462221",
                        "convert_status": "\u5408\u6cd5\u8f6c\u5316",
                        "date": null,
                        "create_time": "1590027506",
                        "remark_dict": {},
                        "address": "",
                        "city_name": "",
                        "module_id": "1667185735022648",
                        "clue_id": "6829116138639589384",
                        "ad_plan_id": "1667194234544220",
                        "form_remark": "",
                        "qq": "",
                        "remark": "",
                        "ad_plan_name": "oCPM_05_20_87",
                        "name": "\u5ed6\u9633\u4e3d",
                        "appname": "\u6296\u97f3",
                        "gender": 0,
                        "age": 0,
                        "cid": 1667202824745079,
                        "telphone": "18057927898",
                        "store_name": "",
                        "location": "\u6d59\u6c5f+\u91d1\u534e",
                        "req_id": "202005211017440100280221412B00AF08",
                        "clue_type": 0,
                        "module_name": "\u8868\u53551",
                        "external_url": "https://www.chengzijianzhan.com/tetris/page/6828807535098462221/",
                        "email": "",
                        "store": {
                            "store_name": "",
                            "store_id": 0,
                            "store_pack_remark": "",
                            "store_pack_name": "",
                            "store_remark": "",
                            "store_address": "",
                            "store_location": "",
                            "store_pack_id": 0
                        },
                        "province_name": ""
                    }
                ]
            }';
    }
}

new Feiyu();
