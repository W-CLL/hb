<?php

namespace app\common\controller;

use think\Session;
use think\Controller;
use think\Db;
use think\Cache;
use think\Request;
use app\common\controller\Auth;

class Common extends Controller
{
    //构造函数
    public function __construct()
    {
        parent::__construct();
        /* RBAC权限校验 */
        if (session('id') == null) {
            $this->error("请登录", "login/login/login");
        }
        $auth = new Auth();
        $request = Request::instance();
        // echo $request->module() . '/' . $request->controller() . '/' . $request->action();
        if (session('type') > 2) {
            $au = $auth->check(strtolower($request->module() . '/' . $request->controller() . '/' . $request->action()), session('type'));
            if (!$au) {
                // dump(strtolower($request->module() . '/' . $request->controller() . '/' . $request->action()));
                // die;
                // 第一个参数是规则名称,第二个参数是用户UID
                $this->error('你没有权限', '/website/site/show/');
            }
        }

        //缓存权限信息
        // if (session('type') > 5 && !session('?auths')) {
        //     $auths = Db::table('auth_group')->find(session('type'));
        //     // var_dump($auths);
        //     session('auths', $auths);
        // }

        //redis缓存下拉框,有就直接用缓存，没就重新读
        #员工下拉选项
        if (!Cache::get('redis_name')) {
            $res = Db::table('user_info ui')
                ->join('user u', 'u.Id=ui.User_Id')
                ->field('ui.User_Id,ui.Name')
                ->where('Type_Id', 'in', [2, 3, 4])
                ->where('Status', 1)
                ->order('Name')
                ->select();
            Cache::set('redis_name', $res);
        }
        #客户下拉选项
        if (!Cache::get('redis_client')) {
            $res = Db::table('user_info ui')
                ->join('user u', 'u.Id=ui.User_Id')
                ->field('ui.Name,ui.User_Id')
                ->where('Type_Id', 5)
                ->where('Status', 1)
                ->order('Name')
                ->select();
            Cache::set('redis_client', $res);
        }
        //负责人负责的客户下拉选项
        if (!Cache::get('redis_user_client' . session('id'))) {
            $res = Db::table('user_info ui')
                ->join('project p', 'p.Client_Id=ui.User_Id')
                ->field('ui.User_Id,ui.Name')
                ->where(function ($query) {
                    $query->where('p.User_Id', session('id'))
                        ->whereOr('See_User_Id', 'like', '%@,' . session('id') . '@,%');
                })
                ->where('Type_Id', 5)
                ->where('Status', 1)
                ->order('Name')
                ->group('Name')
                ->select();
            // var_dump(Db::getLastSql());
            Cache::set('redis_user_client' . session('id'), $res);
        }
        #推广账号下拉选项
        if (!Cache::get('redis_pro_user')) {
            $res = Db::table('promotion_user')
                ->field('Pro_User,Id')
                ->where('Status', 1)
                ->order('Pro_User')
                ->select();
            Cache::set('redis_pro_user', $res);
        }
        #53账号下拉选项
        if (!Cache::get('redis_client_53')) {
            $res = Db::table('client_53')
                ->field('User_53,Id')
                ->order('User_53')
                ->select();
            Cache::set('redis_user_53', $res);
        }

        #项目名下拉选项
        if (!Cache::get('redis_project')) {
            $res = Db::table('project')
                ->where("status", 1)
                ->distinct(true)
                ->field('ProjectName,Id')
                ->order('ProjectName')
                ->select();
            Cache::set('redis_project', $res);
        }

        //合作伙伴下拉选项
        if (!Cache::get('redis_partner')) {
            $res = Db::table('user_info ui')
                ->join('user u', 'u.Id=ui.User_Id')
                ->field('ui.Name,ui.User_Id')
                ->where('Type_Id', 6)
                ->where('Status', 1)
                ->order('Name')
                ->select();
            Cache::set('redis_partner', $res);
        }
    }
}
