<?php
namespace app\customer\controller;

use think\Controller;
use think\Db;
use think\Cache;
use think\Request;
use app\common\controller\Common;
class Customer extends Common
{
    public function _initialize()
    {
        /* 客户下拉选项 */
        if (session('type') <= 2) {
            $this->assign('cli', Cache::get("redis_client"));
        } else {
            //不是管理员只显示自己负责或有权查看的
            $this->assign('cli', Cache::get("redis_user_client" . session('id')));
        }
        $this->assign('type', session('type'));
    }
    /*留言系统视图*/
    public function customer()
    {
        return view();
    }
    /*留言系统数据*/
    public function get_customer()
    {
        //分页数据
        $limit = input('limit');

        //如果是客户只看到自己的数据
        if (session('type') == 5) {
            $data['c.Client_Id'] = session('id');
            $data['Status'] = 1;
        }


        //搜索客户的参数
        if (input('sel_client_id')) {
            $data['c.Client_Id'] = input('sel_client_id');
        }
        if (input('sel_url')) {
            $data['c.Url'] = ['like', "%" . input('sel_url') . "%"];
        }
        if (input('sel_phone')) {
            $data['c.Phone'] = ['like', "%" . input('sel_phone') . "%"];
        }
        if (input('sel_join') == 'repeat') {
            $data['m.Moblie'] = ['not null'];
        } else if (input('sel_join') == 'notrepeat') {
            $data['m.Moblie'] = null;
        }
        //搜索时间范围的参数
        if (input('sel_time')) {
            $time = input('sel_time');
            //分割时间字符串
            $time = explode(' - ', $time);
            //开始日00：00：00时间戳
            $StartTime = strtotime($time[0]);
            //结束日23：59：59时间戳
            $EndTime = strtotime($time[1]) + 86399;
            $data['Time'] = ['between', [$StartTime, $EndTime]];
        }
        //53推送的手机号码 构造子查询，保证Moblie与Phone是索引
        $moblie = Db::table('kf_moblie')
            ->field('Moblie')
            ->group('Moblie')
            ->buildSql();
        /* 项目数据 */
        if (session('type') == 6 || session('type') == 3)  {
            //竞价员右链接所属项目的负责人，即自己只能看到自己负责的项目的客户的数据,还有具有项目可见权限的数据
            $project = Db::table('project')
                ->field('User_Id,Client_Id')
                ->where('User_Id', session('id'))
                ->whereOr('See_User_Id', 'like', '%@,' . session('id') . '@,%')
                ->buildSql();
            $list = Db::table('customer c')
                ->join($moblie . ' m', 'm.Moblie=c.Phone', 'left')
                ->join('user_info u', 'u.User_Id=c.Client_Id')
                ->join($project . ' pj', 'pj.Client_Id=u.User_Id', 'right')
                ->where($data)
                // ->whereOr('pj.See_User_Id','like','%@,'.session('id').'@,%')
                ->field('c.*,u.Name Client,FROM_UNIXTIME(Time,"%Y-%m-%d %T") Sub_Time,Moblie,Ok,Ok_time')
                ->order('Time desc')
                ->group('Id')
                ->paginate($limit);
        } else {
            // $kw = Db::table('kw')->field('Client_Id,Name')->where('Kw', 'like', '%');
            $list = Db::table('customer c')
                ->join($moblie . ' m', 'm.Moblie=c.Phone', 'left')
                ->join('user_info u', 'u.User_Id=c.Client_Id')
                ->where($data)
                ->field('c.*,u.Name Client,FROM_UNIXTIME(Time,"%Y-%m-%d %T") Sub_Time,Moblie,Ok,Ok_time')
                ->order('Time desc')
                ->group('Id')
                ->paginate($limit);
        }
        $list = $list->toArray();
        //查询所有关键词
        $kws = Db::table('kw')
            ->where('Kw', 'not null')
            ->where('Client_Id', 'not null')
            ->select();
        foreach ($list['data'] as $k => $v) {
            if ($list['data'][$k]['Ok_time']) {
                $list['data'][$k]['Ok_time'] = date('Y-m-d H:i:s', $list['data'][$k]['Ok_time']);
            }
            //解码url
            // $list['data'][$k]['Url'] = urldecode($v['Url']);
            $list['data'][$k]['Url'] = $v['Url'];
            $list['data'][$k]['UrlKw'] = getKeyword($v['Url']);
            //得到url的path部分
            $url_path = parse_url($list['data'][$k]['Url'], PHP_URL_PATH);
            if ($url_path == '') {
                $list['data'][$k]['Program'] = '';
            }
            //layui自带的导出功能如果有,会自动换行，所以这里替换下
            $list['data'][$k]['Content'] = str_replace(',', " ", $v['Content']);
            //手机号码重复标识
            if ($v['Moblie']) {
                $list['data'][$k]['Being'] = '重复';
            }
            //留言内容标识
            if ($v['Content'] == "") {
                $list['data'][$k]['Content'] = '无';
            }
            //识别项目
            foreach ($kws as $k2 => $v2) {
                try {
                    $kw2 = $v2['Kw'];
                    $kw2 = str_replace("/", "\/", $kw2);
                    $ismatches = preg_match('/' . $kw2 . '/', $list['data'][$k]['Url'], $matches); //匹配一次，中了就别匹配
                } catch (\Exception $e) {
                    // dump($e);
                }
                //匹配到就终止
                if ($ismatches) {
                    $list['data'][$k]['ProgramName'] = $v2['Name'];
                    break;
                }
            }
        }
        //内部竞价员手机号打*
        if (session('type') == 3  || session('type') == 6) {
            foreach ($list['data'] as $k => $v) {
                if ($list['data'][$k]['Phone']) {
                    for ($i = 3; $i < 7; $i++) {
                        $list['data'][$k]['Phone'][$i] = "*";
                    }
                }
            }
        }

        $res['count'] = $list['total'];
        $res['code'] = 0;
        $res['data'] = $list['data'];
        return $res;
    }
    /* 确认模块 */
    public function ok_customer_do()
    {
        $code = [['code' => 0, 'msg' => '确认成功！'], ['code' => 1, 'msg' => '确认失败！']];
        $data['Id'] = input('Id');
        $list = Db::table('customer')->where($data)->update(['Ok' => 1, 'Ok_time' => time()]);
        //缓存清理
        // Cache::clear();
        return $list ? $code[0] : $code[1];
    }
    /* 永久删除模块 */
    public function dels_customer_do()
    {
        $code = [['code' => 0, 'msg' => '删除成功！'], ['code' => 1, 'msg' => '删除失败,请联系管理员！']];
        if ((input('Id'))) {
            $data['Id'] = input('Id');
        } else {
            $arr = input('data/a')['data'];
            foreach ($arr as $k => $v) {
                $data['Id'][] = $v['Id'];
            }
            $res['Id'] = ['in', $data['Id']];
            unset($data);
            $data = $res;
        }
        if (session('auth') == 3) {
            //普通用户操作为标识被删除状态
            $list = Db::table('customer')->where($data)->update(['Status' => 0]);
        } else {
            //管理员将直接删除数据
            $list = Db::table('customer')->where($data)->delete();
        }
        //缓存清理
        // Cache::clear();
        return $list ? $code[0] : $code[1];
    }
    /*留言统计视图*/
    public function customer_count()
    {
        //默认今天的日期
        $date = date('Y-m-d');
        $this->assign('time', $date);
        return view();
    }
    /*留言统计数据*/
    public function get_customer_count()
    {
        $limit = input('limit');
        $StartTime = strtotime(date('Y-m-d'));
        $data['c.Time'] = ['between', [$StartTime, $StartTime + 86399]];
        $date['Cre_Time'] = ['between', [$StartTime, $StartTime + 86399]];
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;
            $data['c.Time'] = ['between', [$StartTime, $EndTime]];
            $date['Cre_Time'] = ['between', [$StartTime, $EndTime]];
        }
        $push = Db::table('push')
            ->where($date)
            ->field('count(*) Push,Client_Id')
            ->group('Client_Id')
            ->buildSql();
        $customer = Db::table('customer c')
            ->join('kf_moblie kf', 'kf.Moblie=c.Phone', 'left')
            ->where($data)
            ->field('count(Phone) Customer,Client_Id,count(Moblie) Repeats,count(Phone)-count(Moblie) NoRepeats')
            ->group('Client_Id')
            ->buildSql();
        if (empty($EndTime)) {
            $EndTime = $StartTime + 86399;
        }
        $feiyu = Db::table('feiyu_clues fc')
            ->join('feiyu_user fu', 'fc.client_id=fu.Id', 'left')
            ->where('fc.create_time', 'between', [$StartTime, $EndTime])
            ->field('count(clue_id) as Clues,fu.Client_Id')
            ->group('fu.Client_Id')
            ->buildSql();
        if (session('type') <= 2) {
            $list = Db::table('user_info u')
                ->join($customer . ' c', 'c.Client_Id=u.User_Id', 'left')
                ->join($push . ' p', 'p.Client_Id=u.User_Id', 'left')
                ->join($feiyu . ' fy', 'fy.Client_Id=u.User_Id', 'left')
                ->field('u.User_Id,Name,Push,Customer,Repeats,NoRepeats,Clues')
                ->group('u.User_Id')
                ->where('Type_Id', 5)
                ->order('Push', 'desc')
                ->order('Customer', 'desc')
                ->order('Clues', 'desc')
                ->paginate($limit);
        } elseif (session('type') > 2 && session('type') != 5) {
            //竞价员右链接所属项目的负责人，即自己只能看到自己负责的项目的客户的数据
            $project = Db::table('project')
                ->field('User_Id,Client_Id')
                ->where('User_Id', session('id'))
                ->where('Status', 1)
                ->whereOr('See_User_Id', 'like', '%@,' . session('id') . '%')
                ->buildSql();
            $list = Db::table('user_info u')
                ->join($customer . ' c', 'c.Client_Id=u.User_Id', 'left')
                ->join($push . ' p', 'p.Client_Id=u.User_Id', 'left')
                ->join($project . ' pj', 'pj.Client_Id=u.User_Id', 'right')
                ->join($feiyu . ' fy', 'fy.Client_Id=u.User_Id', 'left')
                ->field('u.User_Id,Name,Push,Customer,Repeats,NoRepeats,Clues')
                ->group('u.User_Id')
                ->where('Type_Id', 5)
                ->order('Push', 'desc')
                ->order('Customer', 'desc')
                ->order('Clues', 'desc')
                ->paginate($limit);
        } elseif (session('type') == 5) {
            //客户自己只能看到自己的项目的客户的数据
            $project = Db::table('project')
                ->field('User_Id,Client_Id')
                ->where('Client_Id', session('id'))
                ->buildSql();
            $list = Db::table('user_info u')
                ->join($customer . ' c', 'c.Client_Id=u.User_Id', 'left')
                ->join($push . ' p', 'p.Client_Id=u.User_Id', 'left')
                ->join($project . ' pj', 'pj.Client_Id=u.User_Id', 'right')
                ->join($feiyu . ' fy', 'fy.Client_Id=u.User_Id', 'left')
                ->field('u.User_Id,Name,Push,Customer,Repeats,NoRepeats,Clues')
                ->group('u.User_Id')
                ->where('Type_Id', 5)
                ->order('Push', 'desc')
                ->order('Customer', 'desc')
                ->order('Clues', 'desc')
                ->paginate($limit);
        }

        // $res['sql'] = Db::getLastSql();
        $list = $list->toArray();
        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['Push'] = $v['Push'] ? $v['Push'] : '';
            $list['data'][$k]['Customer'] = $v['Customer'] ? $v['Customer'] : '';
            $list['data'][$k]['Repeats'] = $v['Repeats'] ? $v['Repeats'] : '';
            $list['data'][$k]['NoRepeats'] = $v['NoRepeats'] ? $v['NoRepeats'] : '';
            $list['data'][$k]['Clues'] = $v['Clues'] ? $v['Clues'] : '';
            $list['data'][$k]['Sum'] = $v['Push'] + $v['NoRepeats'] + $v['Clues'];
            $list['data'][$k]['Sum'] = $list['data'][$k]['Sum'] ? $list['data'][$k]['Sum'] : '';
        }
        $res['code'] = 0;
        $res['count'] = $list['total'];
        $res['data'] = $list['data'];
        return $res;
    }
    //更新备注
    public function upd_customer_remark()
    {
        $Id = input('Id');
        $data['Remarks'] = input('Remarks');
        $code = [['code' => 0, 'msg' => '修改成功！'], ['code' => 1, 'msg' => '修改失败！']];
        if (!request()->isPost()) {
            return $code[1];
        }
        $res = Db::table('customer')->where('Id', $Id)->update($data);
        return $res ? $code[0] : $code[1];
    }

}
