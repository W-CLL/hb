<?php

namespace app\kh_info\controller;

use app\common\controller\Common;
use think\Cache;
use think\Db;

/**为统计数据建立关联 */
class KhKw extends Common
{
    public function _initialize()
    {
        $this->assign('cli', Cache::get('redis_client'));
    }

    //统计数据需要建立一些关联，下面都是对关联表的一些操作
    public function kh_kw()
    {
        return view();
    }

    public function get_kh_kw()
    {
        $limit = input('limit');
        $data = Db::table('kh_kw')->alias('kk')
            ->join('user_info ui', 'kk.Client_Id=ui.User_Id', 'left')
            ->join('promotion_user pu', 'pu.Id=kk.Pro_Id')
            ->field('kk.*,ui.Name,pu.Pro_User')
            ->paginate($limit);
        $data = $data->toArray();
        // var_dump($data);
        $res['code'] = 0;
        $res['count'] = $data['total'];
        $res['data'] = $data['data'];
        return $res;
    }

    function ins_kh_kw()
    {
        //找到推广账号列表
        $pro_user = Db::table('promotion_user')->field('Id,Pro_User')->select();
        $this->assign('pro_user', $pro_user);
        return view();
    }

    function ins_kh_kw_do()
    {
        $data['Client_Id'] = input('client_id');
        $data['Pro_Name'] = input('pro_name');
        $data['Tag'] = input('tag');
        $data['Pro_Id'] = input('pro_id');
        $res = Db::table('kh_kw')->insert($data);
        return $res ? ['code' => 0, 'msg' => '添加成功'] : ['code' => 1, 'msg' => '添加失败'];
    }

    function upd_kh_kw()
    {
        $id = input('Id');
        $res = Db::table('kh_kw')->alias('kk')
            ->join('user_info ui', 'ui.User_Id=kk.Client_Id', 'left')
            ->join('promotion_user pu', 'pu.Id=kk.Pro_Id')
            ->field('kk.*,ui.Name,pu.Pro_User')
            ->where('kk.Id', $id)
            ->find();
        $this->assign('list', $res);

        //找到推广账号列表
        $pro_user = Db::table('promotion_user')->field('Id,Pro_User')->select();
        $this->assign('pro_user', $pro_user);

        return view();
    }

    function upd_kh_kw_do()
    {
        $data['Id'] = input('id');
        $data['Client_Id'] = input('client_id');
        $data['Pro_Name'] = input('pro_name');
        $data['Tag'] = input('tag');
        $data['Pro_Id'] = input('pro_id');
        $res = Db::table('kh_kw')->update($data);
        return $res ? ['code' => 0, 'msg' => '更新成功'] : ['code' => 1, 'msg' => '更新失败'];
    }

    function del_kh_kw()
    {
        $data['Id'] = input('Id');
        $res = Db::table('kh_kw')->where('Id', $data['Id'])->delete();
        return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败'];
    }
}
