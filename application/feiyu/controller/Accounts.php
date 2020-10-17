<?php

namespace app\feiyu\controller;

use think\Db;
use think\Cache;
use app\common\controller\Common;

class Accounts extends Common
{
    public function _initialize()
    {

        if (session('type') <= 2) {
            /* 客户下拉选项 */
            $this->assign('cli', Cache::get("redis_client"));
            /* 推广账号下拉选项 */
            $this->assign('pro_user', Cache::get("redis_pro_user"));
        } else {
            //不是管理员只显示自己负责或有权查看的客户
            $this->assign('cli', Cache::get("redis_user_client" . session('id')));

            //如果不是管理员以上权限，只能看到自己所管理的项目推广账号，以及开放可见的推广账号
            $pro_user_ids = Pro_User_Id();
            $this->pro_user_ids = $pro_user_ids; //顺便为之后要用保存一下
            $pro_user_ids = array_flip($pro_user_ids);
            foreach (Cache::get("redis_pro_user") as $v) {
                if (isset($pro_user_ids[$v['Id']])) {
                    $pro_users[] = $v;
                }
            }
            $this->assign('pro_user', $pro_users);
        }
    }
    public function accounts()
    {
        return view();
    }
    public function get_accounts()
    {
        if (session('type') > 2) {
            //不是管理员，只能看到自己有权查看的项目推广账号
            $where['fu.Pro_Id'] = ['in', $this->pro_user_ids];
        }else{
            $where = [];
        }
        $res = Db::table('feiyu_user')
            ->field('fu.*,Pro_User,ui.Name as Client')
            ->alias('fu')
            ->where($where)
            ->join('promotion_user pu', 'pu.Id=fu.Pro_Id', 'left')
            ->join('user_info ui', 'ui.User_Id=fu.Client_Id', 'left')
            ->select();
        $list['code'] = 0;
        $list['data'] = $res;
        $list['count'] = count($res);
        return $list;
    }

    //添加账户模块
    public function add_accounts()
    {
        return view();
    }
    public function add_accounts_do()
    {
        $input['Pro_Id'] = input('pro_id');
        $input['Client_Id'] = input('client_id');
        $input['Name'] = input('name');
        $input['Key'] = input('key');
        $input['Token'] = input('token');
        $res = Db::table('feiyu_user')->insert($input);
        return $res ? ['code' => 0, 'msg' => '添加成功'] : ['code' => 1, 'msg' => '添加失败'];
    }

    //编辑账户模块
    public function upd_accounts()
    {
        $Id = input('id');
        $res = Db::table('feiyu_user')
            ->field('fu.*,Pro_User,ui.Name as Client')
            ->alias('fu')
            ->join('promotion_user pu', 'pu.Id=fu.Pro_Id', 'left')
            ->join('user_info ui', 'ui.User_Id=fu.Client_Id', 'left')
            ->where('fu.Id', $Id)
            ->find();
        $this->assign('account', $res);
        return view();
    }
    //编辑账户模块
    public function upd_accounts_do()
    {
        $data['Id'] = input('id');
        $data['Pro_Id'] = input('pro_id');
        $data['Client_Id'] = input('client_id');
        $data['Name'] = input('name');
        $data['Key'] = input('key');
        $data['Token'] = input('token');
        $res = Db::table('feiyu_user')->update($data);
        return $res ? ['code' => 0, 'msg' => '更新成功'] : ['code' => 1, 'msg' => '更新失败'];
    }

    //删除账户模块
    public function del_accounts_do()
    {
        $Id = input('id');
        $res = Db::table('feiyu_user')->where('Id', $Id)->delete();
        return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败'];
    }

    //更新拉取状态
    public function upd_status()
    {
        $data['Id'] = input('id');

        if (input('status') == '1') {
            $data['status'] = 0;
        } else {
            $data['status'] = 1;
        }
        $res = Db::table('feiyu_user')->update($data);

        return $res ? ['code' => 0, 'msg' => '更新状态成功'] : ['code' => 1, 'msg' => '更新状态失败'];
    }
}
