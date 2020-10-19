<?php

namespace app\promotion\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\common\controller\Common;
use think\Cache;

class Promotion extends Common
{
    public function _initialize()
    {
        $this->assign('type', session('type'));
        //平台下拉选项
        if (!Cache::get('redis_platform')) {
            $res = Db::table('promotion_user')
                ->field('Platform')
                ->where('Platform', 'not null')
                ->where('Platform', '<>', '')
                ->group('Platform')
                ->select();
            Cache::set('redis_platform', $res);
        }
        $this->assign('platform', Cache::get('redis_platform'));
    }
    public function initialize()
    {
    }
    /* 推广账号模块 */
    public function promotion()
    {
        return view();
    }
    public function get_promotion()
    {
        $status['Status'] = 1;
        $limit = input('limit');
        if (input('sel_status') != null) {
            $status['Status'] = input('sel_status');
        }
        if (input('sel_remarks')) {
            $data['Remarks'] = ['like', '%' . input('sel_remarks') . '%'];
        }
        if (input('sel_pro_user')) {
            $data['Pro_User'] = ['like', '%' . input('sel_pro_user') . '%'];
        }
        if (input('sel_platform')) {
            $data['Platform'] = input('sel_platform');
        }
        //聚合查询余额范围
        $having1 = '';
        $having2 = '';
        $having3 = '';
        if (!empty(input('sel_SumMon_min')) || input('sel_SumMon_min') === '0') {
            $having1 = 'SumMon > ' . input('sel_SumMon_min');
        }
        if (!empty(input('sel_SumMon_max') || input('sel_SumMon_max') === '0')) {
            $having2 = 'SumMon < ' . input('sel_SumMon_max');
        }
        if (!empty(input('sel_SumCon7')) || input('sel_SumCon7') === '0') {
            $having3 = 'SumCon7 <= ' . input('sel_SumCon7');
        }
        // 筛选总余额范围
        if ($having1 && $having2) {
            $having =  $having1 . ' and ' . $having2;
        } elseif ($having1 && !$having2) {
            $having =  $having1;
        } elseif (!$having1 && $having2) {
            $having = $having2;
        } else {
            $having = '';
        }
        //筛选近七天消费
        if ($having && $having3) {
            $having .= ' and ' . $having3;
        } elseif (!$having && $having3) {
            $having = $having3;
        }
        //如果不是管理员以上权限，只能看到自己所管理的项目推广账号
        if (session('auth') > 2) {
            $Pro_User_Id = Pro_User_Id();
            $data['Id'] = ['in', $Pro_User_Id];
        }
        $list = Db::table('promotion_user')
            ->where($status)
            ->where($data)
            ->order('Remarks')
            ->paginate($limit);

        $list = $list->toArray();
        $a = $list['data'];
        $pc = Db::table('promotion_con')
            ->field('Pro_Id,Sum(Money_Con) SumCon')
            ->group('Pro_Id')
            ->buildSql();
        $day = date("Y-m-d");
        $day7 = date("Y-m-d", strtotime("-7 day"));
        $pc7 = Db::table('promotion_con')
            ->field('Pro_Id,Sum(Money_Con) SumCon7')
            ->group('Pro_Id')
            ->where('Date', ['between', [$day7, $day]])
            ->buildSql();
        $pr = Db::table('promotion_rec')
            ->where('Status', 'NEQ', '0')
            ->field('Pro_Id,Sum(Money_Rec) SumRec')
            ->group('Pro_Id')
            ->buildSql();
        $p = Db::table('promotion_user p')
            ->join($pc . ' pc', 'p.Id=pc.Pro_Id', 'left')
            ->join($pc7 . ' pc7', 'p.Id=pc7.Pro_Id', 'left')
            ->join($pr . ' pr', 'p.Id=pr.Pro_Id', 'left')
            ->field('p.Id,IFNULL(SumCon,0) SumCon,IFNULL(SumRec,0) SumRec,IFNULL(round(SumCon7/7,2),0) SumCon7,IFNULL(SumRec,0)-IFNULL(SumCon,0) SumMon')
            ->group('p.Id')
            ->having($having)
            ->select();
        foreach ($a as $k => $v) {
            //不要直接输出密码
            // if($v['Pro_Psw']){$a[$k]['Pro_Psw'] = '***';}
            if ($v['Pro_Psw']) {
                $a[$k]['Pro_Psw'] = md5(uniqid());
            }
            foreach ($p as $lk => $lv) {
                if ($lv['SumCon7'] != 0 && $lv['SumMon'] != 0) {
                    $p[$lk]['Day'] = round($lv['SumMon'] / $lv['SumCon7'], 2);
                } else {
                    $p[$lk]['Day'] = 0;
                }

                if ($v['Id'] == $lv['Id']) {
                    //匹配ID追加字段并删除数组索引防止重复
                    $b[] = array_merge($a[$k], $p[$lk]);
                    unset($a[$k]);
                    unset($p[$lk]);
                }
            }
        }
        $res['code'] = 0;
        $res['count'] = $list['total'];
        $res['data'] = $b;
        return $res;
    }
    public function ins_promotion()
    {
        return view();
    }

    public function ins_promotion_do()
    {
        // 插入推广账号
        $data['Pro_User'] = input('pro_user');
        $res = Db::table('promotion_user')
            ->where($data)
            ->find();
        if ($res) {
            return ['code' => 1, 'msg' => '推广账号已存在！'];
        }
        $data['Pro_Psw'] = input('pro_psw');
        $data['Rebate'] = input('rebate');
        $data['Remarks'] = input('remarks');
        $data['Platform'] = input('platform');
        $data['Domain'] = input('domain');
        $data['Client_Rebate'] = input('client_rebate'); //客户返点
        $list = Db::table('promotion_user')->insert($data);
        if ($list) {
            Cache::rm('redis_pro_user');
            return ['code' => 0, 'msg' => '新增推广账号成功！'];
        } else {
            return ['code' => 2, 'msg' => '新增推广账号失败！请联系管理员！'];
        }
    }

    public function del_promotion_do()
    {
        $code1 = [['code' => 0, 'msg' => '停用成功！'], ['code' => 1, 'msg' => '停用失败,请联系管理员！']];
        $code2 = [['code' => 0, 'msg' => '启用成功！'], ['code' => 1, 'msg' => '启用失败,请联系管理员！']];
        $code3 = [['code' => 0, 'msg' => '退款修改成功！'], ['code' => 1, 'msg' => '退款修改失败,请联系管理员！']];
        $code4 = [['code' => 0, 'msg' => '恢复成功！'], ['code' => 1, 'msg' => '恢复失败,请联系管理员！']];


        $Id = input('Id');
        $back = input('Back');

        $list = Db::table('promotion_user')->where('Id', $Id)->field('Status')->find();

        if ($list['Status'] == 1) {
            $status['Status'] = 2;
            $code = $code3;
        } else if ($list['Status'] == 2 && $back == 'b') {
            $status['Status'] = 1;
            $code = $code4;
        } else if ($list['Status'] == 2) {
            $status['Status'] = 0;
            $code = $code1;
        } else {
            $status['Status'] = 1;
            $code = $code2;
        }
        $res = Db::table('promotion_user')->where('Id', $Id)->update($status);
        if ($res) {
            return $code[0];
        } else {
            return $code[1];
        }


    }
    
    public function dels_promotion_do()
    {
        if (session('auth') > 2) {
            return ['code' => 404, 'msg' => '你没有权限'];
        }
        $code = [['code' => 0, 'msg' => '删除成功！'], ['code' => 1, 'msg' => '删除失败,请联系管理员！']];
        $index['Id'] = input('Id');
        $res = Db::table('promotion_user')->where($index)->delete();
        if ($res) {
            Cache::rm('redis_pro_user');
            return $code[0];
        } else {
            return $code[1];
        }
    }

    public function upd_promotion()
    {
        $Id = input('Id');
        $list = Db::table('promotion_user')
            ->where('Id', $Id)
            ->find();
        $this->assign('list', $list);
        return view();
    }
    public function upd_promotion_do()
    {
        $code = [['code' => 0, 'msg' => '编辑成功'], ['code' => 1, 'msg' => '编辑失败,请检查输入内容！']];
        Db::startTrans();
        try {
            $index['Id'] = input('id');
            $data['Remarks'] = input('remarks');
            $data['Pro_User'] = input('pro_user');
            $data['Pro_Psw'] = input('pro_psw');
            $data['Rebate'] = input('rebate');
            $data['Client_Rebate'] = input('client_rebate'); //客户返点
            $data['Platform'] = input('platform');
            $data['Domain'] = input('domain');
            $data['Token'] = input('token');
            $res = Db::table('promotion_user')
                ->where($index)
                ->update($data);
        } catch (\Exception $e) {
            $Trans = true;
        }
        if ($Trans) {
            return $code[1];
        } else {
            Cache::rm('redis_pro_user');
            return $code[0];
        }
    }
    //查看推广账号密码
    public function get_pro_key()
    {
        $pro_user_id = input('Pro_Id');
        $res = Db::table('promotion_user')->field('Pro_Psw')->find($pro_user_id);
        return ['code' => 0, 'key' => htmlspecialchars_decode($res['Pro_Psw'])];
    }
}
