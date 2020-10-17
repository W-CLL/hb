<?php

namespace app\kh_info\controller;

use think\Cache;
use think\Controller;
use think\Db;

// require_once EXTEND_PATH . 'drapisdk_php/sms_service_AccountService.php';
// require_once EXTEND_PATH . 'drapisdk_php/sms_service_ReportService.php';
class Api extends Controller
{
    public function get()
    {
        $Pro_User_Id =  input('Pro_User_Id');
        $info['startDate'] = date('Y-m-d', strtotime(input('startDate')));
        $info['endDate'] = date('Y-m-d', strtotime(input('endDate')));
        $this->assign('Pro_User_Id', $Pro_User_Id);
        $this->assign('info', $info);
        return view();
    }
    //拉取单条信息
    public function pull_info()
    {
        $pro_user_id = input('Pro_User_Id');
        //提供检索数据
        $info['startDate'] = input('startDate');
        $info['endDate'] = input('endDate');
        $info['levelOfDetails'] = input('levelOfDetails');
        $info['reportType'] = input('reportType');

        $pro_user_info = Db::table('promotion_user')->where('Id', $pro_user_id)->find();
        $result = ['code' => 1, 'msg' => '未找到该账号的平台或Token信息'];

        //如果是百度平台的推广账号并且开通了api,即有tokne
        if (preg_match('/百度|baidu/', $pro_user_info['Platform']) && $pro_user_info['Token']) {
            //调用百度实时报告请求api,
            $result = $this->requestBaiduApi($pro_user_info, $info);
        }
        return $result;
    }

    //批量拉取信息
    public function pulls()
    {
        $input = input();
        $data = $input['data'];
        //把所有推广账号的id记录下,方便一次性查询所有信息
        foreach ($data as $k => $v) {
            $pro_user_ids[] = $v['Pro_User_Id'];
        }
        $pro_user_infos = Db::table('promotion_user')->field('Id,Pro_User,Pro_Psw,Platform,Token')->where('Id', 'in', $pro_user_ids)->select();
        // var_dump($pro_user_infos);
        if (!$pro_user_infos) {
            return false;
        }
        //查询账号密码后合并数组
        foreach ($data as $k1 => $v1) {
            foreach ($pro_user_infos as $k2 => $v2) {
                //没有Token就不管了
                // if (empty($v2['Token'])) {
                //     continue;
                // }
                if ($v1['Pro_User_Id'] == $v2['Id']) {
                    $list[] = array_merge($pro_user_infos[$k2], $data[$k1]);
                }
            }
        }
        // 循环调用api
        foreach ($list as $k => $v) {
            //如果是百度平台的推广账号并且开通了api,即有tokne
            if (preg_match('/百度|baidu/', $v['Platform']) && $v['Token']) {

                //调用百度实时报告请求api,
                $result = $this->requestBaiduApi(
                    $list[$k],
                    [
                        'startDate' => date("Y-m-d"),
                        'endDate' => date("Y-m-d"),
                        'levelOfDetails' => '2', 'reportType' => '2'
                    ]
                );
                // $result = ['code' => 0, 'data' => [['id' => 1, 'impression' => 1, 'click' => 23]]];
                // var_dump($list[$k]);
                //如果返回正确的查询数据就合并数组
                if ($result['code'] == 0) {
                    $list[$k] = array_merge($list[$k], $result['data'][0]);
                }
                //合并完成之后清除数组的一些敏感数据
                unset($list[$k]['Pro_Psw']);
                unset($list[$k]['Token']);
            }
        }
        //全部完成后就可以返回数据啦
        return json_encode($list);
        // return json($list, 200, 'Content-type:application/json');
    }

    /**向百度请求数据 返回data中的数据*/
    protected function requestBaiduApi($pro_user_info, $info)
    {
        //获取实时数据报告接口
        $requestUrl = 'https://api.baidu.com/json/sms/service/ReportService/getRealTimeData';
        $requestData['header'] = [
            'username' => $pro_user_info['Pro_User'],
            'password' => $pro_user_info['Pro_Psw'],
            'token' => $pro_user_info['Token'],
        ];
        $requestData['body'] = [
            'realTimeRequestType' => [
                //impression（展现）、click（点击）、cost（花费）、cpc（平均点击价格）、ctr（点击率）、cpm（千次展现成本）、position（首页平均排名）
                'performanceData' => ['impression', 'click', 'cost', 'cpc', 'ctr', 'cpm'],
                //null按照时间排序true降序false升序
                'order' => null,
                'startDate' => $info['startDate'],
                'endDate' => $info['endDate'],
                //指定返回的数据层级2账户粒度3计划粒度5单元粒度7创意粒度11关键词(keywordid)粒度12关键词(keywordid)+创意粒度6关键词(wordid)粒度200高级创意报告粒度
                'levelOfDetails' => $info['levelOfDetails'],
                //针对特定的数据层级设置特定的统计范围，NULL表示统计全部地域
                'attributes' => null,
                //实时数据类型2账户10计划11单元14关键词(keywordid)12创意3地域9关键词(wordid)5二级地域报告38历史数据排名报告
                'reportType' => $info['reportType'],
                //0：全部搜索推广设备1：仅计算机2：仅移动
                'device' => 0,
            ],
        ];
        $json = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        //执行调用
        $res = curlOpen($requestUrl, ['post' => $json, 'ssl' => true]);

        if (!$res) {
            return ['code' => 2, 'msg' => '发送请求失败'];
        }

        $res = json_decode($res, true);

        //转化一下数据格式
        if ($res['header']['status'] == 0) {
            foreach ($res['body']['data'] as $k => $v) {
                $responseDate[$k]['id'] = $v['id'];
                $responseDate[$k]['impression'] = $v['kpis'][0];
                $responseDate[$k]['click'] = $v['kpis'][1];
                $responseDate[$k]['cost'] = $v['kpis'][2];
                $responseDate[$k]['cpc'] = round($v['kpis'][3], 2);
                $responseDate[$k]['ctr'] = round($v['kpis'][4] * 100, 2) . '%';
                $responseDate[$k]['cpm'] = round($v['kpis'][5], 2);
                $v['name'][0] = '';
                $responseDate[$k]['name'] = $v['name'];
                $responseDate[$k]['date'] = $v['date'];
            }
            return ['code' => 0, 'msg' => '请求成功', 'data' => $responseDate];
        } else {
            foreach ($res['header']['failures'] as $v) {
                $msg = $v['message'];
            }
            return ['code' => 3, 'msg' => $msg];
        }
    }

    public function pull_test()
    {
        //{
        //  "header":{
        //      //账户信息
        //  },
        //  "body":{
        //      //具体请求参数，取决于接口
        //      "performanceData":[
        //           impression（展现）、click（点击）、cost（花费）、ctr（点击率）、cpc（平均点击价格）
        //           cpm（千次展现成本）、position（首页平均排名）
        //       ]
        //  }
        //}

        $json = '{
            "url": "/json/sms/service/ReportService/getRealTimeData",
            "header": {
                "username": "baidu-hf广州海豹02B19HY01516",
                "password": "KDKn2220",
                "token": "abe1318d8c2dda03da60c0b2f9de4166",
                "accessToken": "",
                "target": "",
                "authorityType": 0
            },
            "body": {
                "realTimeRequestType": {
                    "unitOfTime": 5,
                    "startDate": "2020-05-21",
                    "reportType": 2,
                    "performanceData": [
                        "impression",
                        "click",
                        "cost",
                        "ctr",
                        "cpc",
                        "cpm",
                        "position"
                    ],
                    "device": null,
                    "statIds": null,
                    "endDate": "2020-05-21",
                    "levelOfDetails": 2
                }
            }
        }';
    }
}
