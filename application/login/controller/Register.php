<?php

namespace app\login\controller;



use think\Controller;

use think\Validate;
use think\Cache;
use think\Db;

class Register extends Controller
{
    //注册功能，暂时只注册type=7，话务
    public function register()
    {
//        return "<h1>暂时关闭注册</h1>";
        $res = array(
            ['code' => 0, 'msg' => '注册账号成功！'],
            ['code' => 1, 'msg' => '用户名已经存在！'],
            ['code' => 2, 'msg' => '注册失败,请联系管理员！'],
        );
        if (request()->isPost()) {
            Db::startTrans();
            try {
                // 插入账号时,判断是否存在了。
                $data['User'] = input('user');
                $user = Db::table('user')->where($data)->find();
                if ($user) {
                    return $res[1];
                }
                $data['Psw'] = md5('abc' . input('password'));
                $data['Cre_time'] = time();
                $data['Login_time'] = time(); //明明是注册为啥要加登录时间？因为这里有个bug。

                $Id = Db::table('user')->insertGetId($data);
                unset($data);

                
                $data['User_Id'] = $Id;
                $data['Name'] = input('username');
                $data['Type_Id'] = 7; //默认话务账户类型
                $data['Auth_Id'] = 3; //默认普通用户
                $data['Alias'] = input('alias');//别名，公司名
                $data['Phone'] = input('phone');//电话
                $data['Ekey'] = md5('cba' . input('username'));

                $info = Db::table('user_info')
                    ->insert($data);
            } catch (\Exception $e) {
                $Trans = true;
                file_put_contents("./log/registerError.txt", $e, FILE_APPEND);
            }
            if ($Trans) {
                Db::rollback();
                return $res[2];
            } else {
                // Cache::clear();
                Db::commit();
                return $res[0];
            }
        }
        return view();
    }
}
