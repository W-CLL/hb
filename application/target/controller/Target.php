<?php

namespace app\target\controller;

use app\common\controller\Common;
use think\Cache;
use think\Db;
use think\Request;
use think\Model;

class Target extends Common
{
    public function _initialize()
    {
        /* 负责人下拉选项 */
        $this->assign('per', Cache::get("redis_name"));
    }
    public function index()
    {
        return view();
    }

    public function target()
    {
        return view();
    }
    public function get_target()
    {
        $limit = input('limit');
        $data['Status'] = 1;

        //非管理员默认看到自己的数据
        // if (session('type') > 2) {
        //     $data['User_Id'] = session('id');
        // }
        //如果有检索条件则接收
        if (input('sel_user_id')) {
            $data['a.User_Id'] = input('sel_user_id');
        }
        if (input('sel_status') != null) {
            $data['Status'] = input('sel_status');
        }
        if (input('sel_time')) { //查询的时间范围

            $time = input('sel_time');
            $time = explode(' - ', $time);

            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;

            $data['Create_time'] = ['between', [$StartTime, $EndTime]];
        }

        //查询数据，管理员查看所有，非管理员查看自己负责的，或有权查看的
        if (session('type') < 3) {
            $targetInfo = Db::table('target a')
                ->where($data)
                ->join('user_info b', 'a.User_Id=b.User_Id', 'left')
                ->field('a.*,b.Name')
                ->order('Create_time', 'desc')
                ->paginate($limit);
        } else {
            $targetInfo = Db::table('target a')
                ->where($data)
                ->where(function ($query) {
                    $query->where("See_User_Id", 'like', '%@,' . session('id') . '@,%')->whereor('a.User_Id', session('id'));
                })
                ->join('user_info b', 'a.User_Id=b.User_Id', 'left')
                ->field('a.*,b.Name')
                ->order('Create_time', 'desc')
                ->paginate($limit);
            // var_dump(Db::getLastSql());
        }
        $targetInfo = $targetInfo->toArray();
        // var_dump($targetInfo)有五个数["total","per_page","current_page","last_page","data"]

        foreach ($targetInfo['data'] as $key => $val) {
            //格式化给前台的时间数据
            $targetInfo['data'][$key]['Create_time'] = date('Y-m-d H:i:s', $val['Create_time']);
            $targetInfo['data'][$key]['End_time'] = date('Y-m-d H:i:s', $val['End_time']);
            // $pattern = "/[@,]+/"; //切割符号标志
            // $userid = $targetInfo['data'][$key]['User_Id'];
            // if (preg_match($pattern, $userid)) {
            //     $userids = explode("@,", $userid);
            // }
            //替换负责人的名字
            // $targetInfo['data'][$key]['Name'] = 1;
        }
        $res['count'] = $targetInfo['total']; //全部记录条数
        $res['code'] = 0;
        $res['data'] = $targetInfo['data'];

        return $res;
    }

    /* 插入模块 */
    public function ins_target()
    {
        return view();
    }

    public function ins_target_do()
    {
        $code = [['code' => 0, 'msg' => '添加成功！'], ['code' => 1, 'msg' => '添加失败,请检查输入内容！'], ['code' => 2, 'msg' => '添加失败,请联系管理员！']];
        Db::startTrans();
        try {
            /* 项目表数据插入 */

            // 用@,拼接所有id
            // $data['User_Id'] = implode("@,", input('user_id/a'));
            $data['See_User_Id'] = '@,' . implode("@,", input('see_user_id/a')) . '@,';

            // $time = input('time');
            // $time = explode(' - ', $time);
            // $data['ExtensionStart'] = $time[0];
            // $data['ExtensionEnd'] = $time[1];
            $data['User_Id'] = input('user_id');
            $data['Work_Target'] = input('work_target');
            $data['Create_time'] = time();
            $data['Remarks'] = input('remarks');
            $data['End_time'] = strtotime(input('end_time'));

            $list = Db::table('target')->insert($data);
            //提交事务
            Db::commit();
        } catch (\Exception $e) {
            file_put_contents("../runtime/TransErrorLog/TargetError.txt", $e . "\r\n", FILE_APPEND);
            $trans_result = true;
            // 回滚事务
            Db::rollback();
        }
        if ($trans_result) {
            return $code[2];
        } else {
            $this->dot($data['User_Id'], input('see_user_id/a'));
            return $code[0];
        }
    }

    /**编辑模块 */
    public function upd_target()
    {
        $Id = input('Id');

        $list = Db::table('target a')
            ->where('a.Id', $Id)
            ->join('user_info b', "a.User_Id=b.User_Id", 'left')
            ->field('a.*,b.Name')
            ->find();

        $seeUsersId = explode("@,", $list['See_User_Id']);

        //格式化时间
        $list['Create_time'] = date('Y-m-d H:i:s', $list['Create_time']);
        $list['End_time'] = date('Y-m-d H:i:s', $list['End_time']);

        //查询可见用户列表
        $see_user = Db::table('user_info')
            ->where("User_Id", "in", $seeUsersId)
            ->field('User_Id,Name')
            ->select();

        $list['see_user'] = $see_user;

        $this->assign('list', $list);

        return view();
    }
    public function upd_target_do()
    {
        $v = input();

        $data['Id'] = $v['Id'];
        //非管理员限制允许更新的数据
        if (session('type') <= 2) {
            $index['User_Id'] = $v['user_id'];
            $index['See_User_Id'] = '@,' . implode("@,", input('see_user_id/a')) . '@,';
            $index['Work_Target'] = $v['work_target'];
            $index['Status'] = $v['status'];
            $index['Remarks'] = $v['remarks'];
            $index['Feedback'] = $v['feedback'];
            $index['End_time'] = strtotime($v['end_time']);
        } else {
            $index['Status'] = $v['status'];
            $index['Feedback'] = $v['feedback'];
        }

        // 更新数据
        $list = Db::table('target')->where($data)->update($index);


        if ($index['User_Id']) {
            //添加消息提示红点
            $this->dot($index['User_Id'], input('see_user_id/a'));
        }


        if (!$list) {
            return ['code' => 1, 'msg' => '更新失败！请检查字段！'];
        } else {
            return ['code' => 0, 'msg' => '更新成功！'];
        }
    }

    /** 永久删除模块 */
    public function dels_target_do()
    {

        $code = [
            ['code' => 0, 'msg' => '删除成功！'],
            ['code' => 1, 'msg' => '删除失败,请联系管理员！'],
            ['code' => 2, 'msg' => '删除失败,非法请求类型']
        ];

        $data['Id'] = input('Id');
        //判断请求类型
        if (!(Request::instance()->isAjax() && Request::instance()->isDelete())) {
            return $code[3];
        }
        $list = Db::table('target')->where($data)->delete();
        return $list ? $code[0] : $code[1];
    }

    /**确认完成操作 */
    public function finsh_target()
    {
        $code = [
            ['code' => 0, 'msg' => '确认成功！'],
            ['code' => 1, 'msg' => '提交失败,请联系管理员！'],
            ['code' => 2, 'msg' => '提交失败,非法请求类型'],
            ['code' => 4, 'msg' => '提交失败,无法查找到该数据，请联系管理员']
        ];
        $data['Id'] = input('Id');
        //判断请求类型
        if (!(Request::instance()->isAjax() && Request::instance()->isPut())) {
            return $code[3];
        }

        $data = Db::table('target')->where($data)->find();
        if (!$data) {
            return $code[4];
        }
        if ($data['Status'] == 1) {
            $newStatus = 0;
        } else {
            $newStatus = 1;
        }
        $list = Db::table('target')->where($data)->update(['Status' => $newStatus, 'Complete_time' => time()]);
        return  $list ? $code[0] : $code[1];
    }

    /**红点提醒 */
    private function dot($userid, $see_user = '')
    {
        try {
            //添加消息提示红点
            $push = new \app\push\controller\Push();
            $push->add_dot(@$userid, 'target', '/target');
            //可见用户也给加上
            if ($see_user != '') {
                foreach ($see_user as $v) {
                    if ($v == $userid) {
                        continue;
                    }
                    $push->add_dot($v, 'target', '/target');
                }
            }
        } catch (\Exception $e) {
            file_put_contents("./log/error.txt", $e . "\r\n", FILE_APPEND);
        }
    }
}
