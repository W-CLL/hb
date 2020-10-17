<?php

namespace app\push\model;

use think\Model;


class KfMoblie extends Model
{

    public function getList($limit, $where = null)
    {
        $list = $this->where($where)->order('Time', 'desc')->paginate($limit, false);
        // $list = $moblie->total();
        $list = $list->toArray();

        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['Time'] = date('Y-m-d H:i:s', $v['Time']);
        }

        $data['data'] = $list['data'];
        $data['code'] = 0;
        $data['count'] = $list['total'];
        return $data;
    }
}
