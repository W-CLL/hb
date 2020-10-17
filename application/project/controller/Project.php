<?php

namespace app\project\controller;

use app\common\controller\Auth;
use think\Controller;
use think\Db;
use think\Cache;
use think\Request;
use app\project\validate;
use app\common\controller\Common;
use think\Loader;
use app\project\model\ProjectLog;
use Exception;

class Project extends Common
{
    public function _initialize()
    {

        if (session('type') <= 2) {
            /* 客户下拉选项 */
            $this->assign('cli', Cache::get("redis_client"));
            /* 推广账号下拉选项 */
            $this->assign('pro_user', Cache::get("redis_pro_user"));
            /* 53账号下拉选项 */
            $this->assign('user_53', Cache::get("redis_user_53"));
        } else {
            //不是管理员只显示自己负责或有权查看的客户
            $this->assign('cli', Cache::get("redis_user_client" . session('id')));

            //如果不是管理员以上权限，只能看到自己所管理的项目推广账号，以及开放可见的推广账号
            $pro_user_ids = Pro_User_Id();
            $pro_user_ids = array_flip($pro_user_ids);
            foreach (Cache::get("redis_pro_user") as $v) {
                if (isset($pro_user_ids[$v['Id']])) {
                    $pro_users[] = $v;
                }
            }
            $this->assign('pro_user', $pro_users);

            /* 53账号下拉选项 */
            $user_53_ids = User_53_Id();
            $user_53_ids = array_flip($user_53_ids);
            foreach (Cache::get("redis_user_53") as $v) {
                if (isset($user_53_ids[$v['Id']])) {
                    $user_53_id[] = $v;
                }
            }
            $this->assign('user_53', $user_53_id);
        }


        /* 负责人下拉选项 */
        $this->assign('per', Cache::get("redis_name"));
        /*合作伙伴下拉选项 */
        $this->assign('part', Cache::get("redis_partner"));
        /* 项目名下拉选项 */
        $this->assign('pro', Cache::get('redis_project'));
        $this->assign('type', session('type'));
    }
    public function project()
    {
        return view();
    }
    public function get_project()
    {
        $limit = input('limit');
        $data['a.Status'] = 1;
        // if (session('type') > 2) {
        //     $data['a.User_Id'] = session('id');
        // }
        if (input('sel_client_id')) {
            $data['a.Client_Id'] = input('sel_client_id');
        }
        if (input('sel_user_id')) {
            $data['a.User_Id'] = input('sel_user_id');
        }
        if (input('sel_status') != null) {
            $data['a.Status'] = input('sel_status');
        }

        //处理搜索项目等级范围
        $sel_grade_min = input('sel_grade_min');
        $sel_grade_max = input('sel_grade_max');
        $str = 'A,B+,B,C+,C,';
        if ($sel_grade_min && !$sel_grade_max) {
            $pos = strpos($str, $sel_grade_min . ',');
            $grade = substr($str, 0, $pos) . $sel_grade_min;
            $data['a.ProjectGrade'] = ['in', $grade];
        }
        if (!$sel_grade_min && $sel_grade_max) {
            $pos = strpos($str, $sel_grade_max . ',');
            $grade = substr($str, $pos, -1);
            $data['a.ProjectGrade'] = ['in', $grade];
        }
        if ($sel_grade_min && $sel_grade_max) {
            $pos_min = strpos($str, $sel_grade_min . ',');
            $pos_max = strpos($str, $sel_grade_max . ',');
            if ($pos_min - $pos_max >= 0) {
                $grade = substr($str, $pos_max, $pos_min - $pos_max) . $sel_grade_min;
                $data['a.ProjectGrade'] = ['in', $grade];
            }
        }

        /* 项目数据 */
        $Client = Db::table('user_info')
            ->where('Type_Id', 5)
            ->field('Name Client,User_Id Client_Id')
            ->group('Client_Id')
            ->buildSql();
        //管理员查看所有内容
        if (session('type') < 3) {
            $p = Db::table('project a')
                ->where($data)
                ->join('user_info b', "b.User_Id=a.User_Id", "left")
                ->join($Client . ' s', "s.Client_Id=a.Client_Id", 'left')
                ->join('client_53 c', "c.Id=a.User_53_Id", "left")
                ->join('promotion_user d', "d.Id=a.Pro_User_Id", "left")
                ->field("a.*,b.Name,s.Client,a.Remarks")
                ->order('a.ProjectName desc')
                ->paginate($limit);
        } else {
            // 非管理员查看自己负责的或有查看权限的，注意这里的$data中是不包含$data['a.User_Id']的，否则影响后面的条件判断
            //like匹配的时候在id前后加上"@,",为了让"312@,512"这样数据存在时id为31或51或12的用户无法匹配到该数据
            $p = Db::table('project a')
                ->where($data)
                ->where(function ($query) {
                    $query->where("a.See_User_Id", 'like', '%@,' . session('id') . '@,%')->whereor('a.User_Id', session('id'));
                })
                ->join('user_info b', "b.User_Id=a.User_Id", "left")
                ->join($Client . ' s', "s.Client_Id=a.Client_Id", 'left')
                ->join('client_53 c', "c.Id=a.User_53_Id", "left")
                ->join('promotion_user d', "d.Id=a.Pro_User_Id", "left")
                ->field("a.*,b.Name,s.Client,a.Remarks")
                ->order('a.ProjectName desc')
                ->paginate($limit);
        }

        /*
            // 查找7天内日期最近的日志
            $subQuery = Db::table('project_log')
                ->where('create_time', '>', strtotime('-7 day'))
                ->buildSql();

            $log = Db::table($subQuery . ' pl')
                ->field('Pro_Id,max(pl.create_time) logtime')
                ->group('pl.Pro_Id')
                ->select();

            foreach ($log as $k => $v) {
                $logtime[$v['Pro_Id']] = $v['logtime'];
            }
            // var_dump($logtime);
            //将最近一次的日志时间添加到列表中
            foreach ($p as $k => $v) {
                // 如果设置了等级并且日志几天内没写就标记一下
                if (isset($logtime[$v['Id']])) {
                    $ti = (time() - $logtime[$v['Id']]) / 86400;
                    switch ($v['ProjectGrade']) {
                        case 'A':
                            $v['logtime'] = $ti > 1 ? floor($ti) : false;
                            break;
                        case 'B+':
                            $v['logtime'] = $ti > 3 ? floor($ti) : false;
                            break;
                        case 'B':
                            $v['logtime'] = $ti > 3 ? floor($ti) : false;
                            break;
                        case 'C+':
                            $v['logtime'] = $ti > 7 ? floor($ti) : false;
                            break;
                        case 'C':
                            $v['logtime'] = $ti > 7 ? floor($ti) : false;
                            break;
                    }
                } else {
                    $v['logtime'] =  false;
                }
                $p[$k] = $v;
            }
        */

        //尝试读取文件,这里用来检测账号是否已分配
        try {
            //这个文件以项目id为建，值为多个账号id和状态的对象
            $ok_status = json_decode(file_get_contents('./static/json/setOkStatus.json'), true);
        } catch (Exception $e) {
            // $e->getMessage();
        }


        if (input('sel_time')) {
            $Date = input('sel_time');
            $date = strtotime($Date) -86399 ;
            $Date = date('Y-m-d', $date);
        } else {
            $Date = date('Y-m-d', strtotime('-1 day'));
        }


        //耦合昨日消费,资源,成本,Entry确认是否录入消费，ConCount已录入的消费记录数，Pro推广账号
        $list = Db::table('project p')
            ->join('promotion_con c', 'p.Id=c.Project_Id', 'left')
            ->where('Date', $Date)
            ->field('Project_Id,Sum(Money_Con) Con,Sum(c.Phone+Message) Res,Sum(c.Cli_Money_Con+c.Cli_Money_Coin) Entry, count(c.Id) ConCount,p.Pro_User_Id Pro')
            ->group('Project_Id')
            ->select();
        $p = $p->toArray();
        $a = $p['data'];

        foreach ($a as $k => $v) {

            //检擦是否有推广账号确认状态
            if (isset($ok_status[$v['Id']])) {
                foreach ($ok_status[$v['Id']] as $puid => $sta) {
                    $a[$k]["OK_Status"] = $sta;
                }
            }

            foreach ($list as $lk => $lv) {
                //推广账号数量
                $list[$lk]['ProCount'] = sizeof(explode('@,', $lv['Pro']));
                if ($v['Id'] == $lv['Project_Id']) {
                    if ($lv['Con'] == 0) {
                        $list[$lk]['Cos'] = 0.00;
                    } else {
                        if ($lv['Res'] != 0) {
                            $list[$lk]['Cos'] = round($lv['Con'] / $lv['Res'], 2);
                        } else {
                            $list[$lk]['Cos'] = 0.00;
                        }
                    }
                    //匹配ID追加字段并删除数组索引防止重复
                    $b[] = array_merge($a[$k], $list[$lk]);
                    unset($a[$k]);
                }
            }
        }
        //没有匹配到的即没有昨日消费，默认为0;
        foreach ($a as $k => $v) {
            // ProCount推广账号数量
            $b[] = array_merge($a[$k], ['Con' => 0.00, 'Res' => 0, 'Cos' => '未录入']);
        }
        
        $res['count'] = $p['total'];
        $res['code'] = 0;
        $res['data'] = $b;

        return $res;
    }
    public function view_project()
    {
        $this->assign('Id', input('Id'));
        return view();
    }
    public function get_view_project()
    {
        $Id = input('Id');
        $Data = Db::table('project a')
            ->where("a.Id", $Id)
            ->join('client_53 c', "c.Id=a.User_53_Id", "left")
            ->join('promotion_user d', "d.Id=a.Pro_User_Id", "left")
            ->field("a.Id,User_53_Id,Pro_User_Id,Code_53,a.Remarks,a.ExtensionStart,a.ExtensionEnd,a.Address,a.Changes,a.Changes_Log")
            ->find();
        $pattern = "/[@,]+/"; //切割符号标志
        foreach ($Data as $k => $v) {
            if (preg_match($pattern, $v)) {
                $Data[$k] = explode("@,", $v);
            } else {
                $Data[$k] = [$v];
            }
        }
        $user_53 = Db::table('client_53')
            ->where("Id", "in", $Data['User_53_Id'])
            ->field('Id,User_53,Psw_53,Remarks')
            ->select();
        $pro_user = Db::table('promotion_user')
            ->where("Id", "in", $Data['Pro_User_Id'])
            ->field('Id,Pro_User,Pro_Psw,Remarks')
            ->select();
        $length = count($user_53) > count($pro_user) ? count($user_53) : count($pro_user);
        $arr = [];

        //尝试读取文件,这里用来检测账号是否已分配
        try {
            //这个文件以项目id为建，值为多个账号id和状态的对象
            $ok_status = json_decode(file_get_contents('./static/json/setOkStatus.json'), true);
            if (isset($ok_status[$Id])) {
                $ok = $ok_status[$Id];
            }
        } catch (Exception $e) {
            // $e->getMessage();
        }

        for ($i = 0; $i < $length; $i++) {
            if (isset($ok)) {
                $arr[$i]['Ok_Status'] = $ok[$pro_user[$i]['Id']];
            } else {
                $arr[$i]['Ok_Status'] = [1, 1];
            }

            if (isset($pro_user[$i])) {
                $arr[$i]['Pro_Id'] = $pro_user[$i]['Id'];
                $arr[$i]['Pro_User'] = $pro_user[$i]['Pro_User'];
                // $arr[$i]['Pro_Psw'] = $pro_user[$i]['Pro_Psw'];//不直接展示密码
                $arr[$i]['Pro_Psw'] = '***';
            } else {
                $arr[$i]['Pro_User'] = null;
                $arr[$i]['Pro_Psw'] = null;
            }
            if (isset($user_53[$i])) {
                $arr[$i]['kf53_Id'] = $user_53[$i]['Id'];
                $arr[$i]['User_53'] = $user_53[$i]['User_53'];
                // $arr[$i]['Psw_53'] = $user_53[$i]['Psw_53'];//不直接展示密码
                $arr[$i]['Psw_53'] = '***';
            } else {
                $arr[$i]['User_53'] = null;
                $arr[$i]['Psw_53'] = null;
            }
            $arr[$i]['Pro_time'] = $Data['ExtensionStart'][0] . "-" . $Data['ExtensionEnd'][0];
        }
        $arr[0]['Code_53'] = $Data['Code_53'][0];
        $arr[0]['Remarks'] = $Data['Remarks'][0];
        $arr[0]['Address'] = $Data['Address'][0];

        $res['data'] = $arr;
        $res['code'] = 0;
        $res['Changes'] = $Data['Changes'];
        $res['Changes_Log'] = $Data['Changes_Log'];
        $res['id'] = $Data['Id'][0];
        return $res;
    }
    /* 插入模块 */
    public function ins_project()
    {
        return view();
    }

    public function ins_project_do()
    {
        $code = [['code' => 0, 'msg' => '添加成功！'], ['code' => 1, 'msg' => '添加失败,请检查输入内容！'], ['code' => 2, 'msg' => '添加失败,请联系管理员！']];
        Db::startTrans();
        try {
            /* 项目表数据插入 */
            $data['Client_Id'] = input('client_id');
            $data['User_Id'] = input('user_id');
            // 用@,拼接所有id
            $data['See_User_Id'] = '@,' . implode("@,", input('see_user_id/a')) . '@,';
            $data['Pro_User_Id'] = implode("@,", input('pro_user_id/a'));
            $data['User_53_Id'] = implode("@,", input('user_53_id/a'));
            $data['Code_53'] = input('code_53');
            $data['ProjectName'] = input('projectname');
            $time = input('time');
            $time = explode(' - ', $time);
            $data['ExtensionStart'] = $time[0];
            $data['ExtensionEnd'] = $time[1];
            $data['Remarks'] = input('remarks');
            $data['EstimatedCost'] = input('estimatedcost');
            $data['CustomerBudget'] = input('customerbudget');
            $data['Address'] = input('address');
            $data['ProjectGrade'] = input('projectgrade');
            $data['TargetNumber'] = input('targetnumber');

            $data['Brand'] = input('brand');

            $data['Changes_Log'] = date('Y-m-d H:i:s').' '.session('username').'：创建项目'."\n";

            $Project_Id = Db::table('project')->insertGetId($data);
           
            $this->dot($data['User_Id'], input('see_user_id/a'));

            $ok_status = json_decode(file_get_contents('./static/json/setOkStatus.json'), true);
            $new = explode('@,', $data['Pro_User_Id']);
            foreach ($new as $pro_user_id) {
                $pro_user[$pro_user_id] = [0, 0];
            }
            $ok_status[$Project_Id] = $pro_user;
            file_put_contents('./static/json/setOkStatus.json', json_encode($ok_status));
        } catch (\Exception $e) {
            file_put_contents("projecterror.txt", $e . "\r\n", FILE_APPEND);
            $trans_result = true;
        }
        if ($trans_result) {
            Db::rollback();
            return $code[2];
        } else {
            Cache::rm('redis_project');
            Db::commit();
            return $code[0];
        }
    }

    /* 停用项目模块 */
    public function del_project_do()
    {
        $code1 = [['code' => 0, 'msg' => '停用成功！'], ['code' => 1, 'msg' => '停用失败,请联系管理员！']];
        $code2 = [['code' => 0, 'msg' => '启用成功！'], ['code' => 1, 'msg' => '启用失败,请联系管理员！']];
        $data['Id'] = input('Id');
        $list = Db::table('project')->where($data)->field('Status')->find();
        if ($list['Status'] == 1) {
            $status['Status'] = 0;
            $code = $code1;
        } else {
            $status['Status'] = 1;
            $code = $code2;
        }
        $list = Db::table('project')->where($data)->update($status);
        return $list ? $code[0] : $code[1];
    }
    /* 永久删除模块 */
    public function dels_project_do()
    {
        $code = [['code' => 0, 'msg' => '删除成功！'], ['code' => 1, 'msg' => '删除失败,请联系管理员！']];
        $data['Id'] = input('Id');
        $list = Db::table('project')->where($data)->delete();
        Cache::rm('redis_project');
        return $list ? $code[0] : $code[1];
    }
    /* 编辑处理 */
    public function upd_project()
    {
        $Id = input('Id');
        $data = Db::table('project')->where('Id', $Id)->find();
        $data['See_User_Id'] = explode("@,", $data['See_User_Id']);
        $data['Pro_User_Id'] = explode("@,", $data['Pro_User_Id']);
        $data['User_53_Id'] = explode("@,", $data['User_53_Id']);
        $Client = Db::table('user_info')
            ->where('Type_Id', 5)
            ->field('Name Client,User_Id Client_Id')
            ->group('User_Id')
            ->buildSql();
        $list = Db::table('project a')
            ->where('a.Id', $Id)
            ->join('user_info b', "b.User_Id=a.User_Id", "left")
            ->join($Client . ' s', 's.Client_Id=a.Client_Id', 'left')
            ->join('client_53 c', "c.Id=a.User_53_Id", "left")
            ->join('promotion_user d', "d.Id=a.Pro_User_Id", "left")
            ->field("a.*,b.Name,Client")
            ->find();
        $user_53 = Db::table('client_53')
            ->where("Id", "in", $data['User_53_Id'])
            ->field('Id,User_53,Psw_53')
            ->select();
        $pro_user = Db::table('promotion_user')
            ->where("Id", "in", $data['Pro_User_Id'])
            ->field('Id,Pro_User,Pro_Psw')
            ->select();
        //查询可见用户列表
        $see_user = Db::table('user_info')
            ->where("User_Id", "in", $data['See_User_Id'])
            ->field('User_Id,Name')
            ->select();
        $list['pro_user'] = $pro_user;
        $list['user_53'] = $user_53;
        $list['see_user'] = $see_user;
        $this->assign('list', $list);
        return  view();
    }
    public function upd_project_do()
    {
        $v = input();
        $Data['Id'] = $v['Id'];
        $index['Client_Id'] = $v['client_id'];
        $index['User_Id'] = $v['user_id'];
        $index['See_User_Id'] = '@,' . implode("@,", input('see_user_id/a')) . '@,';
        $index['Pro_User_Id'] = implode("@,", input('pro_user_id/a'));
        $index['User_53_Id'] = implode("@,", input('user_53_id/a'));
        $index['Code_53'] = input('code_53');
        $index['ProjectName'] = $v['projectname'];
        $time = input('time');
        $time = explode(' - ', $time);
        $index['ExtensionStart'] = $time[0];
        $index['ExtensionEnd'] = $time[1];
        $index['Remarks'] = $v['remarks'];
        $index['EstimatedCost'] = $v['estimatedcost'];
        $index['CustomerBudget'] = $v['customerbudget'];
        $index['Address'] = $v['address'];
        $index['ProjectGrade'] = $v['projectgrade'];
        $index['TargetNumber'] = $v['targetnumber'];
        $index['Brand'] = $v['brand'];


        $old_list = Db::table('project')->where($Data)->find();

        $ok_status = json_decode(file_get_contents('./static/json/setOkStatus.json'), true);

        //循环检查数据是否有变化，有的话就记录下
        foreach ($old_list as $key => $val) {
            if (empty($index[$key]) || $val == $index[$key] || $key == 'Changes') {
                continue;
            }
            switch ($key) {
                case 'Changes':
                    continue;
                case 'Remarks':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改备注： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'ProjectName':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改项目名称： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'ExtensionStart':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改推广开始时间： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'ExtensionEnd':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改推广结束时间： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'Pro_User_Id':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改推广账号： "  . $val . '--&gt;' . $index[$key] . " \n";

                    //对新增推广账号给出标记提示
                    $new = explode('@,', $index[$key]);
                    foreach ($new as $pro_user_id) {
                        if (isset($ok_status[$Data['Id']][$pro_user_id])) {
                            $pro_user[$pro_user_id] = $ok_status[$Data['Id']][$pro_user_id];
                        } else {
                            $pro_user[$pro_user_id] = [0, 0];
                        }
                    }
                    $ok_status[$Data['Id']] = $pro_user;
                    try {
                        file_put_contents('./static/json/setOkStatus.json', json_encode($ok_status));
                    } catch (Exception $e) {
                    }

                    break;
                case 'User_53_Id':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改53账号： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'EstimatedCost':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改预计成本： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'CustomerBudget':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改客户预算： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'Code_53':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改项目53代码： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'Address':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改地域： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'ProjectGrade':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改项目等级： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'TargetNumber':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改目标数量： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
                case 'Brand':
                    $index['Changes'] .= date('Y-m-d H:i:s', time()) . " 已修改品牌： "  . $val . '--&gt;' . $index[$key] . " \n";
                    break;
            }
        }

        if ($index['Changes']) {
            $index['Changes_Log'] = $old_list['Changes_Log'] . $index['Changes'];
        }
        $list = Db::table('project')->where($Data)->update($index);
        if (!$list) {
            return ['code' => 1, 'msg' => '更新失败！请检查字段！'];
        } else {
            #更新缓存
            Cache::rm('redis_project');
            //红点提醒
            $this->dot($v['user_id'], input('see_user_id/a'));
            return ['code' => 0, 'msg' => '更新成功！'];
        }
    }

    //已修改Changes字段
    public function upd_project_changes()
    {
        $id = input('id');
        $data['Changes'] = input('changes');
        if (!empty($data['Changes'])) {
            $info = Db::table('project')->where('Id', $id)->field('Changes,Changes_Log')->find();
            //去除首尾空白转义后对比
            if (trim(htmlspecialchars_decode($info['Changes'])) == trim(htmlspecialchars_decode($data['Changes']))) {
                return ['code' => 1, 'msg' => '文本字段未修改，请删除文本框内的文字再点击确定按钮'];
            }
            $data['Changes_Log'] = $info['Changes_Log'] . "\n" . $data['Changes'] . "\n";
        }
        $result = Db::table('project')->where('Id', $id)->update($data);
        return $result ? ['code' => 0, 'msg' => '更新成功'] : ['code' => 1, 'msg' => '更新失败，请联系管理员'];
    }


    //项目日志模块
    public function log_project()
    {
        $Pro_Id = input('Id');
        $log = new ProjectLog();
        $logs =  $log
            ->where('Pro_Id', $Pro_Id)
            ->alias('pl')
            ->join('user_info ui', 'ui.User_Id=pl.User_Id')
            ->order('pl.create_time desc')
            ->field('pl.*,ui.Name')
            ->select();

        //将日志按日期划分
        foreach ($logs as $k => $v) {
            $data = date('Y-m-d', $v['create_time']);
            $list[$data][] = [
                'create_time' => $v['create_time'],
                'Content' => $v['Content'],
                'Name' => $v['Name']
            ];
        }

        $this->assign('Pro_Id', $Pro_Id);
        $this->assign('list', $list);

        return view();
    }

    public function log_project_add()
    {
        $input = input();

        $input['User_Id'] = session('id');
        $log = new ProjectLog($input);
        $count = $log->allowField(true)->save();

        $res = ['code' => 0, 'msg' => '添加成功', 'count' => $count];
        if (!$count) {
            $res['code'] = -2;
            $res['msg'] = '添加失败，请联系管理员';
        }
        return json($res);
    }

    /**红点提醒 */
    private function dot($userid, $see_user = '')
    {
        try {
            //添加消息提示红点
            $push = new \app\push\controller\Push();
            $push->add_dot(@$userid, 'project', '/project');
            //可见用户也给加上
            if ($see_user != '') {
                foreach ($see_user as $v) {
                    if ($v == $userid) {
                        continue;
                    }
                    $push->add_dot($v, 'project', '/project');
                }
            }
        } catch (\Exception $e) {
            file_put_contents("./log/error.txt", $e . "\r\n", FILE_APPEND);
        }
    }

    /**修改推广账号的确认状态 ，运营和维护*/
    public function ok_status()
    {
        //这个文件以项目id为建，值为多个账号id和状态的对象
        $ok_status = json_decode(file_get_contents('./static/json/setOkStatus.json'), true);

        $input = input();
        $input['Pro_User_Id'];
        $input['Project_Id'];

        if (isset($ok_status[$input['Project_Id']])) {
            if (isset($input['ok_0'])) {
                $ok_0 = $input['ok_0'];
                $ok_status[$input['Project_Id']][$input['Pro_User_Id']][0] = 1;
                $change_log =  date('Y-m-d H:i:s')  . ' 运营确认：' . session('username') . "\n";
            }
            if (isset($input['ok_1'])) {
                $ok_1 = $input['ok_1'];
                $ok_status[$input['Project_Id']][$input['Pro_User_Id']][1] = 1;
                $change_log =  date('Y-m-d H:i:s') . ' 维护确认：' . session('username') . "\n";
            }
        }
        $code = file_put_contents('./static/json/setOkStatus.json', json_encode($ok_status));

        // UPDATE `project`  SET `Changes_Log`=concat(Changes_Log,'2020-07-24 15:45:36 维护已确认\n')  WHERE  `Id` = 168;
        $list = Db::table('project')
            ->where('Id', $input['Project_Id'])
            ->update(['Changes_Log' => Db::raw("concat(Changes_Log,'$change_log')")]);

        if ($code) {
            return ['code' => 0, 'msg' => '确认成功'];
        } else {
            return ['code' => 1, 'msg' => '确认失败'];
        }
    }
}
