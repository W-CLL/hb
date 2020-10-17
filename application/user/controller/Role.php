<?php

namespace app\user\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\paginator\driver\Bootstrap;
use think\Cache;
use app\common\controller\Common;

/**
 * 角色管理
 */
class Role extends Common
{
    public function _initialize()
    {
        if (!session('type') <= 2) {
            return ['msg' => '你没有权限', 'code' => 1];
        }
    }

    // 添加角色
    public function add_role()
    {
        #获取请求对象
        $request = Request::instance();

        if ($request->isPut()) {
            if (!$request->has('Type_Name', 'put')) {
                return ['code' => -2, 'msg' => '请输入角色名称'];
            }
            $data['Type_Name'] = $request->param('Type_Name');

            // 创建用户前需要先创建一个该角色的权限分组
            $data['Group_Id'] = Db::table('auth_group')->insertGetId(['status' => 1, 'rules' => '1']);
            $data['Type_Id'] = $data['Group_Id'];

            $res = Db::table('user_type')->insert($data);

            if ($res) {
                return ['code' => 0, 'msg' => '创建成功'];
            }
        } elseif ($request->isGet()) {
            return view();
        }
    }

    //删除角色
    public function del_role()
    {
        #获取请求对象
        $request = Request::instance();

        if ($request->isDelete() && session('type') <= 2) {

            $id = $request->param('Type_Id');
            if ($id <= 5) {
                return ['code' => 1, 'msg' => '无法删除基础角色'];
            }
            //先查找该角色下是否有账号
            $user = model('UserInfo')
                ->join('user', 'user.Id = user_info.User_Id', 'right')
                ->where('Type_Id', $id)
                ->select();

            // var_dump($user);
            if ($user) {
                return ['code' => 1, 'msg' => '无法删除，因为该角色下存在用户'];
            }
            // 权限分组表的聚合模型，关联删除角色信息
            $authGroup = model('AuthGroup')->get($id);
            $res = $authGroup->delete();
            return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败'];
        }
    }

    /**控制左侧菜单显示 */
    public function leftmenu($group_id)
    {
        $request = Request::instance();

        if ($request->isGet()) {
            //获取菜单
            $leftMenu = json_decode(file_get_contents('static/json/leftmenu/basemenu.json'), true);
            if (is_file('static/json/leftmenu/' . $group_id . '.json')) {
                $hidden = json_decode(file_get_contents('static/json/leftmenu/' . $group_id . '.json'), true);
                //将当前菜单状态写入
                foreach ($hidden as $k => $v) {
                    $leftMenu[$k]['hidden'] = $v['hidden'];
                    foreach ($v['list'] as $k2 => $v2) {
                        $leftMenu[$k]['list'][$k2]['hidden'] = $v2['hidden'];
                    }
                }
            }

            $this->assign('leftmenu', json_encode($leftMenu));
            $this->assign('group_id', $group_id);
            return view('leftmenu');
        }

        if ($request->isPost()) {
            $json = html_entity_decode($request->param('data'));
            file_put_contents('./static/json/leftmenu/' . $group_id . '.json', $json);
            return ['code' => 0, 'msg' => '修改成功'];
        }
    }
}
