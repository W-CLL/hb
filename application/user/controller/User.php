<?php

namespace app\user\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\paginator\driver\Bootstrap;
use think\Cache;
use app\common\controller\Common;

class User extends Common
{

    public function _initialize()
    {
        $Auth = Db::table('user_auth')
            ->where('Auth_Id', '>', session('auth'))
            ->select();
        $Name = Db::table('user_info a')
            ->where('Auth_Id', '>', session('auth'))
            ->field('User_Id Id,Name')
            ->select();
        $Type = Db::table('user_type')
            ->where('Type_Id', '>', session('type'))
            ->order('Type_Id desc')
            ->select();
        $this->assign('name', $Name);
        $this->assign('type_name', $Type);
        $this->assign('auth_name', $Auth);
        $this->assign('type', session('type'));
        $this->assign('auth', session('auth'));
    }
    public function user()
    {
        return view();
    }
    public function get_user()
    {
        $limit = input('limit');
        if (input('sel_status') != null) {
            $data['Status'] = input('sel_status');
        }
        if (input('sel_type_id')) {
            $data['Type_Id'] = input('sel_type_id');
        }
        $list = Db::table('user a')
            ->join('user_info b', 'a.Id=b.User_Id')
            ->where($data)
            ->where('b.Type_Id', '>', session('type'))
            ->field('Id,User,Login_time,Last_time,Ip,Status,Name,Type_Id,Auth_Id,Phone,Alias,Ekey,Msg_service')
            ->order('a.Id')
            ->paginate($limit);
        $data = $list->toArray();
        $list = setValue($list->all());
        $res['count'] = $data['total'];
        $res['code'] = 0;
        $res['data'] = $list;
        return $res;
    }
    public function ins_user()
    {
        return view();
    }

    public function ins_user_do()
    {
        $res = array(
            ['code' => 0, 'msg' => '新增账号成功！'],
            ['code' => 1, 'msg' => '新增失败,账号已经存在！'],
            ['code' => 2, 'msg' => '新增错误,请联系管理员！'],
        );
        Db::startTrans();
        try {
            //插入账号时,判断是否存在了。
            $data['User'] = input('user');
            $user = Db::table('user')->where($data)->find();
            if ($user) {
                return $res[1];
            }
            $data['Psw'] = md5('abc' . input('psw'));
            $data['Cre_time'] = time();
            $data['Login_time'] = time();
            $Id = Db::table('user')->insertGetId($data);
            unset($data);
            $data['User_Id'] = $Id;
            $data['Name'] = input('name');
            $data['Type_Id'] = input('type_id');
            $data['Auth_Id'] = 3; //默认普通用户
            $data['Alias'] = input('alias');
            $data['Phone'] = input('phone');
            $data['Ekey'] = md5('cba' . input('user'));
            $info = Db::table('user_info')
                ->insert($data);
        } catch (\Exception $e) {
            $Trans = true;
            file_put_contents("error.txt", $e, FILE_APPEND);
        }
        if ($Trans) {
            Db::rollback();
            return $res[2];
        } else {
            Cache::rm('redis_name');
            Cache::rm('redis_client');
            Db::commit();
            return $res[0];
        }
    }
    //冻结账号操作
    public function del_user_do()
    {
        $Id = input('Id');
        $Status = input('Status');

        // 冻结
        if ($Status == 0) {
            $status['Status'] = 0;
            $code = [['code' => 0, 'msg' => '停用成功！'], ['code' => 1, 'msg' => '停用失败,请联系管理员！']];
        }
        // 启用
        if ($Status == 1) {
            $status['Status'] = 1;
            $code = [['code' => 0, 'msg' => '启用成功！'], ['code' => 1, 'msg' => '启用失败,请联系管理员！']];
        }
        // 隐藏(下拉列表不显示)
        if ($Status == 2) {
            $status['Status'] = 2;
            $code = [['code' => 0, 'msg' => '隐藏成功！'], ['code' => 1, 'msg' => '隐藏失败,请联系管理员！']];
        }

        $res = Db::table('user')
            ->where('Id', $Id)
            ->update($status);
            
        if ($res) {
            Cache::rm('redis_name');
            Cache::rm('redis_client');
            return $code[0];
        } else {
            return $code[1];
        }
    }

    public function dels_user_do()
    {
        $code = [['code' => 0, 'msg' => '删除成功！'], ['code' => 1, 'msg' => '删除失败,请联系管理员！']];
        $Id = input('Id');
        $res = Db::table('user')
            ->where('Id', $Id)
            ->delete();
        // $res = Db::table('user_info')
        //     ->where('User_Id', $Id)
        //     ->delete();
        Cache::rm('redis_name');
        Cache::rm('redis_client');
        return $res ? $code[0] : $code[1];
    }
    public function reset_user()
    {
        $Id = input('Id');
        $list = Db::table('user')
            ->where('Id', $Id)
            ->field('Id,User')
            ->find();
        $this->assign('list', $list);
        return view();
    }
    public function reset_user_do()
    {
        $code = [['code' => 0, 'msg' => '更新成功！'], ['code' => 2, 'msg' => '更新失败,请联系检查输入！']];
        $Id = input('Id');
        $data['User'] = input('user');
        $data['Psw'] = md5('abc' . input('psw'));
        $res = Db::table('user')
            ->where('Id', $Id)
            ->update($data);
        return $res ? $code[0] : $code[1];
    }
    //编辑账号视图
    public function upd_user()
    {
        $Id = input('Id');
        $list = Db::table('user_info')
            ->where('User_Id', $Id)
            ->field('User_Id,Phone,Alias,Name,Auth_Id,Type_Id,Ekey,Msg_service')
            ->find();
        Cache::rm('redis_name');
        Cache::rm('redis_client');
        $list = setValue($list);
        $this->assign('list', $list);
        return view();
    }
    //编辑账号操作
    public function upd_user_do()
    {
        $res = array(
            ['code' => 0, 'msg' => '编辑账号成功！'],
            ['code' => 1, 'msg' => '编辑错误,请联系管理员！'],
        );
        $index['User_Id'] = input('user_id');
        $data['Name'] = input('name');
        $data['Phone'] = input('phone');
        $data['Alias'] = input('alias');
        $data['Type_Id'] = input('type_id');
        $data['Ekey'] = input('ekey');
        //off的时候会空值，有点坑
        $data['Msg_service'] = input('msg_service') ? input('msg_service') : "off";
        $list = Db::table('user_info')
            ->where($index)
            ->update($data);
        if ($list) {
            Cache::rm('redis_name');
            Cache::rm('redis_client');
            return $res[0];
        } else {
            return $res[1];
        }
    }
    //个人修改信息视图
    public function upd_msg()
    {
        $Id = session('id');
        $list = Db::table('user')
            ->where('Id', $Id)
            ->field('User,Id')
            ->find();
        $this->assign('list', $list);
        return view();
    }
    //个人修改信息操作
    public function upd_msg_do()
    {
        $code = [['code' => 0, 'msg' => "修改成功！"], ['code' => 1, 'msg' => "修改失败！请联系管理员！"]];
        $Id = input('id');
        $data['Psw'] = md5('abc' . input('psw'));
        $res = Db::table('user')
            ->where('Id', $Id)
            ->update($data);
        Cache::rm('redis_name');
        Cache::rm('redis_client');
        return $res ? $code[0] : $code[1];
    }
}
