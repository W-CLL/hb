<?php

namespace app\wxapi\controller;

use think\Config;
// use app\wxapi\model\JSSDK;
use think\Session;
use think\Controller;
use think\Db;
use think\Validate;
use think\Cache;

class Api extends Controller
{
   //接收token登录
    public function wx_login_do()
    {
        // 这里接收临时uid,和token,然后和限时缓存中的记录对比,缓存中包含临时token和真实uid
        $token = input('token');
        $uid = input('uid');
        //根据临时uid找到对应缓存，然后对比token,通过则更新真实用户登录数据
        $cache = Cache::get('login_'.$uid);
        if($token != $cache['token']){
            return ['code'=>1,'msg'=>'token错误'];
        }
        //根据真实uid找到对应的用户信息
          $res = Db::table('user a')
            ->where('Id',$cache['uid'])
            ->join('user_info b', 'b.User_Id=a.Id')
            ->field('a.Id,User,Login_time,Ip,a.Status,Name,Type_Id,Auth_Id,Alias')
            ->find();
            
        if(!$res){return ["msg" => "未找到用户信息!，请先登录账号绑定微信", "code" => 2];}
        
         #登录成功
        if ($res['Status'] == '0') {
            return ["msg" => "账号已经被冻结，请联系管理员解封!", "code" => 3];
        }
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
        session('auth', $res['Auth_Id']); // 表示权限
        session('open_window',1);//初次登录弹出窗口,弹出一次后记得使用session('open_window',null)清除
        
        Cache::rm('login_'.$uid);//登录之后记得销毁临时数据
        return ["msg" => "登录成功!", "code" => 0];
    }
}