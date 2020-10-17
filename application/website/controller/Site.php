<?php

namespace app\website\controller;

use app\common\controller\Common;
use think\Cookie;
use think\Db;
use think\Validate;

class Site extends Common
{
    public function __construct()
    {
        parent::__construct();
        //为了区分cookie,加上前缀
        Cookie::init(['prefix' => 'uid' . session('id') . '_', 'expire' => 3600]);
        if (!Cookie::has('site_public', 'public_')) {
            //公共站点，user_id为0的即是公共站点，不属于任何用户
            $res = Db::table('website')->where('User_Id', 0)->select();
            Cookie::set('site_public', $res, ['prefix' => 'public_', 'expire' => 3600]);
        }
        if (!Cookie::has('site_self')) {
            //个人收藏站点
            $res = Db::table('website')->where('User_Id', session('id'))->select();
            Cookie::set('site_self', $res);
        }
        if (session('type') < 5) {
            if (!Cookie::has('site_inside', 'public_')) {
                //内部收藏站点
                $res = Db::table('website')->where('User_Id', -1)->select();
                Cookie::set('site_inside', $res, ['prefix' => 'public_', 'expire' => 3600]);
            }
        }
        $this->assign('site_public', Cookie::get('site_public', 'public_'));
        $this->assign('site_self', Cookie::get('site_self'));
        $this->assign('site_inside', Cookie::get('site_inside', 'public_'));
    }

    public function index()
    {
        return view();
    }

    public function show()
    {
        return view();
    }

    //添加公共站点
    public function add_site_public()
    {
        if (!(session('type') < 3)) {
            return '添加公共站点请联系管理员';
        }
        if (request()->isPost()) {
            $data['User_Id'] = 0; //user_id为0 的即是公共站点
            $data['Site_Url'] = input('siteurl');
            $data['Site_Name'] = input('sitename');

            if (!$data['Site_Url'] || !$data['Site_Name']) {
                return ['code' => 1, 'msg' => '请检查输入内容'];
            }
            //如果没有设置协议就手动加上,默认都加https
            if (!preg_match('/http:\/\/|https:\/\//', $data['Site_Url'])) {
                $data['Site_Url'] = 'https://' . $data['Site_Url'];
            }
            //得到网站图标地址
            $data['Site_Icon'] = $this->getIconUrl($data['Site_Url']);

            if (!$data['Site_Icon']) {
                ['code' => 1, 'msg' => '输入地址有误'];
            }
            $res = Db::table('website')->insert($data);
            Cookie::delete('site_public', 'public_');
            return $res ? ['code' => 0, 'msg' => '操作成功'] : ['code' => 1, 'msg' => '操作失败'];
        }
        return view();
    }

    //添加个人站点收藏
    public function add_site()
    {
        if (request()->isPost()) {
            $data['User_Id'] = session('id');
            $data['Site_Url'] = input('siteurl');
            $data['Site_Name'] = input('sitename');

            if (!$data['Site_Url'] || !$data['Site_Name']) {
                return ['code' => 1, 'msg' => '请检查输入内容'];
            }
            //如果没有设置协议就手动加上,默认都加https
            if (!preg_match('/http:\/\/|https:\/\//', $data['Site_Url'])) {
                $data['Site_Url'] = 'https://' . $data['Site_Url'];
            }
            //得到网站图标地址
            $data['Site_Icon'] = $this->getIconUrl($data['Site_Url']);

            if (!$data['Site_Icon']) {
                ['code' => 1, 'msg' => '输入地址有误'];
            }
            $res = Db::table('website')->insert($data);
            Cookie::delete('site_self');
            return $res ? ['code' => 0, 'msg' => '操作成功'] : ['code' => 1, 'msg' => '操作失败'];
        }
        return view();
    }

    //添加个人站点收藏
    public function add_site_inside()
    {
        if (request()->isPost()) {
            $data['User_Id'] = -1;
            $data['Site_Url'] = input('siteurl');
            $data['Site_Name'] = input('sitename');

            if (!$data['Site_Url'] || !$data['Site_Name']) {
                return ['code' => 1, 'msg' => '请检查输入内容'];
            }
            //如果没有设置协议就手动加上,默认都加https
            if (!preg_match('/http:\/\/|https:\/\//', $data['Site_Url'])) {
                $data['Site_Url'] = 'https://' . $data['Site_Url'];
            }
            //得到网站图标地址
            $data['Site_Icon'] = $this->getIconUrl($data['Site_Url']);

            if (!$data['Site_Icon']) {
                ['code' => 1, 'msg' => '输入地址有误'];
            }
            $res = Db::table('website')->insert($data);
            Cookie::delete('site_self');
            return $res ? ['code' => 0, 'msg' => '操作成功'] : ['code' => 1, 'msg' => '操作失败'];
        }
        return view();
    }

    //删除站点
    public function del_site()
    {
        if (!request()->isPost()) {
            return ['code' => 1, 'msg' => '操作错误'];
        }
        $data['Id'] = input('siteid');
        if (input('public') == 1) {
            if (!(session('type') < 3)) {
                return ['code' => 1, 'msg' => '删除此内容请联系管理员'];
            }
            $data['User_Id'] = 0;
        } elseif (input('public') == -1) {
            $data['User_Id'] = -1;
        } else {
            $data['User_Id'] = session('id');
        }
        $result = Db::table('website')->where($data)->delete();
        Cookie::delete('site_self');
        Cookie::delete('site_public', 'public_');
        Cookie::delete('site_inside', 'public_');
        return $result ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败，请联系管理员'];
    }

    // 更新站点
    public function upd_site()
    {
        if (request()->isPost()) {
            $data['Id'] = input('id');
            $data['User_Id'] = input('userid');
            $data['Site_Name'] = input('sitename');
            $data['Site_Url'] = input('siteurl');
            $data['Site_Icon'] = input('siteicon');

            //验证数据
            if ($data['User_Id'] == 0 && session('type') > 2) {
                //公共导航只有管理员才能改
                return ['code' => 1, 'msg' => '修改该导航请联系管理员'];
            }
            $validate = new Validate(['Site_Name' => 'require', 'Site_Url' => 'require|url', 'Site_Icon' => 'require|url']);
            if (!$validate->check($data)) {
                return ['code' => 1, 'msg' => '请求失败' . $validate->getError()];
            }
            $res = Db::table('website')->where('Id', $data['Id'])->update($data);
            Cookie::delete('site_self');
            Cookie::delete('site_public', 'public_');
            Cookie::delete('site_inside', 'public_');
            return $res ? ['code' => 0, 'msg' => '更新成功'] : ['code' => 1, 'msg' => '请求失败,请联系管理员'];
        }
        $data['Id'] = input('siteid');
        $public = input('public');
        //如果是公共导航并且不是管理员就不让修改
        if ($public && session('type') > 2) {
            return '修改该导航需要联系管理员';
        }
        $res = Db::table('website')->find($data['Id']);
        $this->assign('site', $res);
        return view();
    }

    /** 得到网站图标地址 */
    private function getIconUrl($url)
    {
        // 解析url,获取站点图标地址
        $parse = parse_url($url);
        if (!$parse) {
            return false;
        }
        if ($parse['scheme'] == 'https') {
            return "https://" . $parse['host'] . "/favicon.ico";
        } else {
            return "http://" . $parse['host'] . "/favicon.ico";
        }
    }
}
