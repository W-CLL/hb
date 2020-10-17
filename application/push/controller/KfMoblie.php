<?php

namespace app\push\controller;

use app\common\controller\Common;
use think\Db;
use app\push\model\KfMoblie as ModelKfMoblie;


class KfMoblie extends Common
{
    // 53客服推送的所有的电话
    public function kfmoblie()
    {
        return view('kf_moblie');
    }
    public function getKfMoblie()
    {
        if (session('type') > 2) {
            return ['code' => 1, 'msg' => '没有权限'];
        }
        $limit = input('limit');

        //查询的时间范围
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;
            $where['Time'] = ['between', [$StartTime, $EndTime]];
        }
        //查询的电话号
        if (input('sel_phone')) {
            $where['Moblie'] = ['like', '%' . input('sel_phone') . '%'];
        }

        $model = new ModelKfMoblie();
        $result = $model->getList($limit, $where);

        return json($result);
    }

    // 留言,推送,飞鱼的全部历史电话
    public function history()
    {
        return view();
    }

    public function getHistory()
    {
        if (!(session('type') <= 2)) {
            return ['code' => 1, 'msg' => '你没有权限'];
        }
        $where = null;

        $page = input('page');
        $limit = input('limit');

        //查询的时间范围
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;
            $where['Cre_time'] = ['between', [$StartTime, $EndTime]];
        }
        //查询的电话号
        if (input('sel_phone')) {
            $where['Content'] = ['like', '%' . input('sel_phone') . '%'];
        }

        $cu = Db::table('customer')
            ->field('Id,Client_Id,"留言表单" as ColumnType,Phone as Content,Time as Cre_time')
            ->union(function ($query) {
                $query->table('push')
                    ->field('Id,Client_Id,"推送记录" as ColumnType,Content as Content,Cre_time');
            }, true)
            ->union(function ($query) {
                $query->table('feiyu_clues')
                    ->field('Id,user_id as Client_Id,"飞鱼线索" as ColumnType,telphone as Content,create_time as Cre_time');
            }, true)
            ->buildSql();

        $client = Db::table('user_info')
            ->field('User_Id as Client_Id,Name')
            ->buildSql();

        $result = Db::table($cu . ' T')
            ->order('Cre_time desc')
            ->where($where)
            ->join($client . ' ui', 'ui.Client_Id=T.Client_Id', 'left')
            ->paginate(['page' => $page, 'list_rows' => $limit]);

        $result = $result->toArray();

        foreach ($result['data'] as $k => $v) {
            $result['data'][$k]['Cre_time'] = date('Y-m-d H:i:s', $v['Cre_time']);
            if ($v['ColumnType'] == '推送记录') {
                $mobile = $this->findThePhoneNumbers($v['Content']);
                $phone = [];
                foreach ($mobile as $value) {
                    $phone[] = $value['number'];
                }
                if (!empty($phone)) {
                    $result['data'][$k]['Content'] = $phone;
                }
            }
        }

        $data['data'] = $result['data'];
        $data['code'] = 0;
        $data['count'] = $result['total'];

        return $data;
    }

    public function findThePhoneNumbers($Str = "")
    {
        header("content-type:text/plain;charset=utf-8");
        // 检测字符串是否为空
        $Str = trim($Str);
        if (empty($Str)) {
            return false;
        }
        // 手机号的获取
        // $reg = "/^1[34578]\d{9}$/"; //匹配数字的正则表达式
        $reg = '/\D1[3-9]\d{9}(\D|$)/';
        preg_match_all($reg, $Str, $result);
        $nums = array();
        $ca = "/^1((34[0-8]\d{7})|((3[0-3|5-9])|(4[5-7|9])|(5[0-3|5-9])|(66)|(7[1-3|5-8])|(8[0-9])|(9[1|8|9]))\d{8})$/";
        foreach ($result[0] as $key => $value) {
            $value = substr($value, 1, 11);
            // var_dump($value);
            if (preg_match($ca, $value)) {
                $nums[] = array("number" => $value, "type" => "");
            } else {
            }
        }
        // 返回最终数组
        return $nums;
    }
}
