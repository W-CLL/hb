<?php

namespace app\user\controller;

use AlibabaCloud\Cloudwf\V20170328\DelUmengPagePermission4Root;
use think\Controller;
use think\Db;
use think\Request;
use think\Cache;
use app\common\controller\Common;

class Rbac extends Controller
{
    public function _initialize()
    {
        //不是管理员权限滚蛋
        if (session('auth') > 2) {
            return ['msg' => '你没有权限', 'code' => 1];
        }
    }
    public function rbac()
    {
        return view();
    }
    public function get_rbac()
    {
        $list = Db::table('user_type ut')
            ->where('Type_Id', '>=', session('type'))
            ->select();
        $res['code'] = 0;
        $res['data'] = $list;
        return $res;
    }

    public function ins_rbac()
    {
        return view();
    }
    public function ins_rbac_do()
    {
        $code = [['code' => 0, 'msg' => '新增成功'], ['code' => 1, 'msg' => '新增失败']];
        $data['name'] = input('name');
        $data['title'] = input('title');
        $res = Db::table('auth_rule')
            ->insert($data);
        return $res ? $code[0] : $code[1];
    }

    //编辑账号视图
    public function upd_rbac()
    {
        //查找所有权限
        $allAuth = model('AuthRule')
            ->field('id,title,name,P_Id')
            ->where('status', 1)
            ->select();
        //查找已拥有的权限
        $hasAuth = model('AuthGroup')
            ->where('id', input('Group_Id'))
            ->find();
        // 分割
        $arr = explode(',', $hasAuth['rules']);
        // var_dump($arr);
        foreach ($allAuth as $k => $v) {

            $checked = false;
            if (in_array($v['id'], $arr)) { //大海捞针
                $checked = true;
            }

            //根据路由的模块名划分
            $m =  explode('/', $v['name']);
            $auth = ['id' => $v['id'], 'title' => $v['title'], 'checked' => $checked];

            if ($m[0] == 'index') {
                $auths['index'][] = $auth;
            } elseif ($m[0] == 'website') {
                $auths['website'][] = $auth;
            } elseif ($m[0] == 'project') {
                $auths['project'][] = $auth;
            } elseif ($m[0] == 'client_rec') {
                $auths['client_rec'][] = $auth;
            } elseif ($m[0] == 'promotion') {
                $auths['promotion'][] = $auth;
            } elseif ($m[0] == 'client_53') {
                $auths['client_53'][] = $auth;
            } elseif ($m[0] == 'kw') {
                $auths['kw'][] = $auth;
            } elseif ($m[0] == 'customer') {
                $auths['customer'][] = $auth;
            } elseif ($m[0] == 'feiyu') {
                $auths['feiyu'][] = $auth;
            } elseif ($m[1] == 'push') {
                $auths['push'][] = $auth;
            } elseif ($m[1] == 'kf_moblie') {
                $auths['kf_moblie'][] = $auth;
            } elseif ($m[0] == 'kh_info') {
                $auths['kh_info'][] = $auth;
            } elseif ($m[0] == 'talkdata') {
                $auths['talkdata'][] = $auth;
            } elseif ($m[0] == 'target') {
                $auths['target'][] = $auth;
            } elseif ($m[0] == 'user') {
                $auths['user'][] = $auth;
            } elseif ($m[0] == 'wxapi') {
                $auths['wxapi'][] = $auth;
            }
        }
        $this->assign('auths', $auths);
        $this->assign('Group_Id', input('Group_Id'));
        return view();
    }

    //编辑账号操作
    public function upd_rbac_do()
    {
        $request = Request::instance();
        if (!$request->isPost()) {
            return ['code' => 0, 'msg' => '请求方式错误'];
        }
        $value = $request->param();
        $rules = implode(',', array_keys($value['rule_ids']));
        $res = model('AuthGroup')->where('id', $value['Group_Id'])->update(['rules' => $rules]);
        $code = array(
            ['code' => 0, 'msg' => '修改成功！'],
            ['code' => 1, 'msg' => '修改错误,请联系管理员！'],
        );
        return $res ? $code[0] : $code[1];
    }


    //权限列表
    public function auth_list()
    {
        $request = Request::instance();
        if ($request->isGet()) {
            if (!$request->has('limit')) {
                return view();
            } else {
                $input = $request->param();
                $where = [];
                if (isset($input['sel_title'])) {
                    $where['title'] = ['like', '%' . $input['sel_title'] . '%'];
                }
                if (isset($input['sel_name'])) {
                    $where['name'] = ['like', '%' . $input['sel_name'] . '%'];
                }
                $list = model('AuthRule')
                    ->field('id,name,title')
                    ->where($where)
                    ->paginate(['list_rows' => $input['limit'], 'page' => $input['page']]);

                $list = $list->toArray();
                return ['code' => 0, 'count' => $list['total'], 'data' => $list['data']];
            }
        }
    }
    //更新一条权限
    public function upd_auth()
    {
        $request = Request::instance();
        //get请求
        if ($request->isGet()) {
            $id = $request->param('id');
            $res = model('AuthRule')->find($id);
            $this->assign('auth', $res);
            return view();
        }
        //post请求更新数据
        if ($request->isPost()) {
            $input = $request->param();
            $res = model('AuthRule')->allowField(true)->update($input);
            return $res ? ['code' => 0, 'msg' => '更新成功'] : ['code' => 1, 'msg' => '更新失败'];
        }
    }
    // 删除一条权限
    public function del_auth()
    {
        $request = Request::instance();
        if ($request->isDelete() && session('type') <= 2) {
            $res = model('AuthRule')->where($request->param())->delete();
            return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败'];
        }
    }
}
