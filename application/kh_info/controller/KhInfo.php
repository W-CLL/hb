<?php

namespace app\kh_info\controller;

use app\common\controller\Common;
use think\Db;


class KhInfo extends Common
{
    public function kh_info()
    {
        return view();
    }
    public function get_kh_info()
    {
        $limit = input('limit');
        $search = null;
        //查询访客参数条件
        if (input('sel_guest_id')) {
            $search['guest_id'] = ['like', "%" . input('sel_guest_id') . "%"];
        }
        if (input('sel_worker_id')) {
            $search['worker_id'] = ['like', "%" . input('sel_worker_id') . "%"];
        }
        if (input('sel_land_page')) {
            $search['land_page'] = ['like', "%" . input('sel_land_page') . "%"];
        }
        //查询时间范围
        $start_time = input('sel_start_time');
        $end_time = input('sel_end_time');
        if ($start_time && $end_time) {
            $search['time'] = ['between', [strtotime($start_time), strtotime($end_time)]];
        } elseif ($start_time && !$end_time) {
            $search['time'] = ['>=', strtotime($start_time)];
        } elseif (!$start_time && $end_time) {
            $search['time'] = ['<=', strtotime($end_time)];
        }
        if (input('sel_is_mobile') == '1') {
            // $search['mobile'] = ['NOT NULL', 1];
            $search['mobile'] = ['<>', ''];
        } elseif (input('sel_is_mobile') == '0') {
            // $search['mobile'] = ['null', 1];
            $search['mobile'] = ['=', ''];
        }
        // var_dump($search);

        if (empty($search)) {
            $data = Db::table('kh_info')->order('time', 'desc')->paginate($limit);
        } else {
            $data = Db::table('kh_info')->where($search)->order('time', 'desc')->paginate($limit);
            // echo Db::getLastSql();
        }
        $data = $data->toArray();

        //处理查询到的数据
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['time'] = date('Y-m-d H:i:s', $v['time']);
            // 使用设备为手机时为2其他为1
            if ($v['device'] == '2') {
                $data['data'][$k]['device'] = '手机';
            } else {
                $data['data'][$k]['device'] = '其他';
            }
            $data['data'][$k]['from_page'] = urldecode($v['from_page']);
            $data['data'][$k]['land_page'] = urldecode($v['land_page']);
            $data['data'][$k]['talk_page'] = urldecode($v['talk_page']);
            //隐藏手机号中间4位
            $data['data'][$k]['mobile'] = $v['mobile']?substr_replace($v['mobile'],'****','3','4'):'';
        }

        $res['code'] = 0;
        $res['count'] = $data['total'];
        $res['data'] = $data['data'];
        // $res['sql'] = Db::getLastSql();
        return $res;
    }

    //删除单个53访客信息
    public function del_kh_info()
    {
        if (session('type') > 2) {
            return ['code' => 1, 'msg' => '删除失败,你没有权限'];
        }
        $Id = input('id');
        if (!empty($Id)) {
            $res = Db::table('kh_info')->where('Id', $Id)->delete();
        }
        return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败,请联系管理员'];
    }
    //删除多条访客信息
    public function dels_kh_info()
    {
        if (session('type') > 2) {
            return ['code' => 1, 'msg' => '删除失败,你没有权限'];
        }
        $ids = input('ids/a');
        // var_dump($ids);
        if (!empty($ids)) {
            $res = Db::table('kh_info')->where('Id', 'IN', $ids)->delete();
        }
        return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败,请联系管理员'];
    }
}
