<?php

namespace app\login\controller;



use think\Controller;

use think\Validate;
use think\Cache;
use think\Db;


class Login extends Controller
{

    public function login()
    {
        return view();
    }


    public function checklogin()
    {


        $data['User'] = input('username');

        $data['Psw'] = md5("abc" . input('password'));

        #查询用户信息


        $res = Db::table('user a')

            ->where($data)

            ->join('user_info b', 'b.User_Id=a.Id')

            ->field('a.Id,User,Login_time,Ip,a.Status,Name,Type_Id,Auth_Id,Alias')

            ->find();

        if ($res == null) {
            #密码或者账号错误空
            return ["msg" => "用户名或密码错误!", "code" => 2];
        } else {

            #登录成功
            if ($res['Status'] == '0') {
                #Status判定
                return ["msg" => "账号已经被冻结，请联系管理员解封!", "code" => 3];
            } else {

                #更新登录数据
                $upd['Login_time'] = time();

                $upd['Ip'] = getIp();

                $login = Db::table('user')

                    ->where('Id', $res['Id'])

                    ->update($upd);

                session('id', $res['Id']); // 表示ID

                session('type', $res['Type_Id']); // 账号类型

                if ($res['Type_Id'] == 5) {
                    session('username', $res['Alias']); // 用户名
                } else {
                    session('username', $res['Name']); // 用户名
                }

                session('auth', $res['Auth_Id']); // 表示ID

                session('open_window', 1); //初次登录弹出窗口,弹出一次后记得使用session('open_window',null)清除

                //如果绑定了微信也设置上
                $weixin = Db::table('wx_user')->where('User_Id', $res['Id'])->field('OpenID')->find();
                if (isset($weixin['OpenID'])) {
                    session('openid', $weixin['OpenID']);
                }

                return ["msg" => "登录成功!", "code" => 1];
            }
        }
    }



    public function loginout()
    {
        #销毁全部登录信息
        session(null);
        $this->redirect('/public/index.php/login');
    }
}
