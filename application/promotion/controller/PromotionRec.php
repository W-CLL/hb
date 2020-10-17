<?php

namespace app\promotion\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\common\controller\Common;
use think\Cache;
use think\Model;

class PromotionRec extends Common
{
    public function _initialize()
    {
        $this->assign('type', session('type'));
    }
    /* 推广账号充值模块 */
    public function promotion_rec()
    {
        return view();
    }
    public function get_promotion_rec()
    {
        $limit = input('limit');
        if (input('sel_remarks')) {
            $data['p.Remarks'] = ['like', '%' . input('sel_remarks') . '%'];
        }
        if (input('sel_pro_user')) {
            $data['Pro_User'] = ['like', '%' . input('sel_pro_user') . '%'];
        }
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;
            $data['Cre_time'] = ['between', [$StartTime, $EndTime]];
        }
        if (input('sel_status') || input('sel_status') === '0') {
            $data['c.Status'] = input('sel_status');
        }
        //如果不是管理员以上权限，只能看到自己所管理的项目推广账号
        if (session('auth') > 2) {
            $Pro_User_Id = Pro_User_Id();
            $data['c.Pro_Id'] = ['in', $Pro_User_Id];
        }

        $day = date("Y-m-d");
        $day7 = date("Y-m-d", strtotime("-7 day"));
        //近七日消费统计
        $pc7 = Db::table('promotion_con')
            ->field('Pro_Id,Sum(Money_Con) SumCon7')
            ->group('Pro_Id')
            ->where('Date', ['between', [$day7, $day]])
            ->buildSql();
        //充值总金额统计
        $pr = Db::table('promotion_rec')
            ->where('Status', 'NEQ', '0')
            ->field('Pro_Id,Sum(Money_Rec) SumRec')
            ->group('Pro_Id')
            ->buildSql();
        //消费总金额统计
        $pc = Db::table('promotion_con')
            ->field('Pro_Id,Sum(Money_Con) SumCon')
            ->group('Pro_Id')
            ->buildSql();

        $list = Db::table('promotion_rec c')
            ->join('user_info u', 'u.User_Id=c.User_Id')
            ->join('promotion_user p', 'p.Id=c.Pro_Id')
            ->join($pc . ' pc', 'p.Id=pc.Pro_Id', 'left')
            ->join($pc7 . ' pc7', 'p.Id=pc7.Pro_Id', 'left')
            ->join($pr . ' pr', 'p.Id=pr.Pro_Id', 'left')
            ->field('c.Id,Name,Pro_User,Money_Rec,Cre_time,p.Remarks,c.Remarks RecRemarks,Rec_B,c.Status,IFNULL(round(SumCon7/7,2),0) SumCon7,IFNULL(SumRec,0)-IFNULL(SumCon,0) SumMon')
            ->where($data)
            ->order('Cre_time desc')
            ->paginate($limit);

        $list = $list->toArray();
        $data = $list['data'];
        foreach ($data as $k => $v) {
            $data[$k]['Cre_time'] = date('Y-m-d H:i:s', $v['Cre_time']);
            
            // 转化充值状态
            switch ($data[$k]['Status']) {
                case '0':
                    $data[$k]['Status'] = '未打款';
                    break;
                case '1':
                    $data[$k]['Status'] = '已打款';
                    break;
                case '2':
                    $data[$k]['Status'] = '已到账';
                    break;
                case '3':
                    $data[$k]['Status'] = '退款';
                    break;
            }
        }
        $res['count'] = $list['total'];
        $res['data'] = $data;
        $res['code'] = 0;
        return $res;
    }
    //新增充值记录视图
    public function ins_promotion_rec()
    {
        $Id = request()->param('Id');
        $list = Db::table('promotion_user')
            ->where('Id', $Id)
            ->field('Id,Remarks,Pro_User,Rebate')
            ->find();
        $this->assign('list', $list);
        return view();
    }
    //新增充值记录操作
    public function ins_promotion_rec_do()
    {
        $code = [['code' => 0, 'msg' => '账号新增充值记录成功！'], ['code' => 1, 'msg' => '账号充值失败！请联系管理员！']];
        $data['Pro_Id'] = input('id');
        $data['Money_Rec'] = input('money_rec');
        $data['Cre_time'] = time();
        $data['Remarks'] = input('remarks');
        $data['User_Id'] = session('id');
        $data['Rec_B'] = intval((float) strval(input('money_rec')) * (float) strval(input('rebate')));

        $data['Details'] = date("Y-m-d H:i:s") . ' ' . session('username') . "：新增充值记录<br>\n\r";
        $res = Db::table('promotion_rec')
            ->insert($data);
        return $res ? $code[0] : $code[1];
    }
    //删除充值记录操作
    public function dels_promotion_rec_do()
    {
        $code = [['code' => 0, 'msg' => '删除充值记录成功！'], ['code' => 1, 'msg' => '删除充值记录失败！请联系管理员！']];
        $index['Id'] = input('Id');
        $res = Db::table('promotion_rec')
            ->where($index)
            ->delete();
        return $res ? $code[0] : $code[1];
    }
    //编辑推广账号充值记录
    public function upd_promotion_rec()
    {
        $Id = input('Id');
        $list = Db::table('promotion_rec r')
            ->join('promotion_user u', 'r.Pro_Id=u.Id')
            ->where('r.Id', $Id)
            ->field('r.Id,Money_Rec,Cre_time,Pro_User,r.Remarks,Rec_B,r.Status')
            ->find();
        $this->assign('list', $list);
        return view();
    }
    //编辑推广账号充值记录操作
    public function upd_promotion_rec_do()
    {
        // $code = [['code' => 0, 'msg' => '编辑充值记录成功！'], ['code' => 1, 'msg' => '编辑充值记录失败！请联系管理员！']];
        $index['Id'] = input('id');
        $data['User_Id'] = session('id');
        $data['Remarks'] = input('remarks');
        $data['Status'] = input('status');
        $data['Details'] = date("Y-m-d H:i:s") . ' ' . session('username') . "：编辑充值记录<br>\n\r";
        //只有管理员才接受以下数据
        if (session('type') < 3) {
            $data['Money_Rec'] = input('money_rec');
            $data['Cre_time'] = strtotime(input('cre_time'));
            $data['Rec_B'] = input('rec_b');
        }
        if (($data['Status'] == '0' || $data['Status'] == '1') && session('type') > 2) {
            return ['code' => 2, 'msg' => '编辑充值记录失败！你不是管理员！'];
        }
        $model = model('PromotionRec');
        $res = $model->upd_promotion_rec_do($index, $data);
        // $res = Db::table('promotion_rec')
        //     ->where($index)
        //     ->update($data);
        //返回false 表示没有出错
        if ($res != false) {
            return ['code' => 1, 'msg' => '编辑充值记录失败！' . $res];
        }
        return ['code' => 0, 'msg' => '编辑充值记录成功！'];
    }

    // 已打款按钮
    public function havepay_promotion_rec_do()
    {
        $index['Id'] = input('Id');
        // $data['Remarks'] = '已打款';
        $data['User_Id'] = session('id');
        $data['Status'] = '1';
        $data['Details'] = date("Y-m-d H:i:s") . ' ' . session('username') . "：已打款<br>\n\r";

        $model = model('PromotionRec');
        $res = $model->havepay_promotion_rec_do($index, $data);
        //返回false 表示没有出错
        if ($res != false) {
            return ['code' => 1, 'msg' => '操作失败！' . $res];
        }
        return ['code' => 0, 'msg' => '操作成功！'];
    }
    //已到账按钮
    public function havebill_promotion_rec_do()
    {
        $index['Id'] = input('Id');
        // $data['Remarks'] = '已到账';
        $data['User_Id'] = session('id');
        $data['Status'] = '2';
        $data['Details'] = date("Y-m-d H:i:s") . ' ' . session('username') . "：已到账<br>\n\r";
        $model = model('PromotionRec');
        $res = $model->havebill_promotion_rec_do($index, $data);
        //返回false 表示没有出错
        if ($res != false) {
            return ['code' => 1, 'msg' => '操作失败！' . $res];
        }
        return ['code' => 0, 'msg' => '操作成功！'];
    }
    //查看充值记录操作详情
    public function detail_promotion_rec()
    {
        $id = input('Id');
        $dataInfo = Db::table('promotion_rec')->where('Id', $id)->field('Details')->find();
        echo $dataInfo['Details'];
    }
}
