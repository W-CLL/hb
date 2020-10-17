<?php
namespace app\client_53\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Cache;
use app\common\controller\Common;
class Client_53 extends Common
{
    public function _initialize()
    {
        /* 客户下拉选项 */
        $this->assign('cli', Cache::get("redis_client"));
        $client = Db::table('user_info')
            ->where('Type_Id', 5)
            ->field('User_Id,Name')
            ->select();
        $this->assign('client', $client);
    }

    public function client_53()
    {
        return view();
    }
    
    public function get_client_53()
    {
        $limit = input('limit');
        if (input('sel_user_53')) {
            $data['User_53'] = ['like', '%' . input('sel_user_53') . '%'];
        }
        if (input('sel_remarks')) {
            $data['Remarks'] = ['like', '%' . input('sel_remarks') . '%'];
        }
        //如果不是管理员以上权限，只能看到自己所管理的53账号
        if (session('auth') > 2) {
            $User_53_Id = User_53_Id();
            $data['c.Id'] = ['in', $User_53_Id];
        }
        $list = Db::table('client_53 c')
            ->join('user_info u', 'c.Client_Id=u.User_Id', 'left')
            ->field('c.*,u.Name')
            ->where($data)
            ->order('c.Id')
            ->paginate($limit);
        $list = $list->toArray();
        $res['count'] = $list['total'];
        $res['data'] = $list['data'];
        $res['code'] = 0;
        return $res;
    }

    public function ins_client_53()
    {
        return view();
    }

    public function ins_client_53_do()
    {
        $code = [
            ["code" => 0, "msg" => "新增53快服账号成功!"],
            ["code" => 1, "msg" => "新增失败,请账号已存在!"],
            ["code" => 2, "msg" => "新增失败,请联系管理员"],
        ];
        $data['Client_Id'] = input('client_id');
        $data['User_53'] = input('user_53');
        $data['Psw_53'] = input('psw_53');
        $data['Remarks'] = input('remarks');
        #判断53账号是否有重复
        $list = Db::table('client_53')->where('User_53', input('user_53'))->find();
        if (!$list) {
            #没有就新增
            $res = Db::table('client_53')->insert($data);
            Cache::rm('redis_client_53');
            return $res ? $code[0] : $code[2];
        } else {
            return $code[1];;
        }
    }

    public function dels_client_53_do()
    {
        if (session('auth') > 2) {
            return ['code' => 404, 'msg' => '你没有权限'];
        }
        $code = [
            ["code" => 0, "msg" => "删除53快服账号成功!"],
            ["code" => 1, "msg" => "删除失败,请练习管理员!"],
        ];
        $index['Id'] = input('Id');
        $list = Db::table('client_53')->where($index)->delete();
        Cache::rm('redis_client_53');
        return $list ? $code[0] : $code[1];
    }

    public function upd_client_53()
    {
        $Id = input('Id');
        $list = Db::table('client_53 c')
            ->join('user_info u', 'c.Client_Id=u.User_Id', 'left')
            ->where('Id', $Id)
            ->field('c.*,u.Name')
            ->find();

        $this->assign('list', $list);
        return view();
    }

    public function upd_client_53_do()
    {
        $code = [['code' => 0, 'msg' => '编辑成功！'], ['code' => 1, 'msg' => '编辑失败,请联系管理员!']];
        $index['Id'] = input('id');
        $data['Client_Id'] = input('client_id');
        $data['User_53'] = input('user_53');
        $data['Psw_53'] = input('psw_53');
        $data['Remarks'] = input('remarks');
        $res = Db::table('client_53')
            ->where($index)
            ->update($data);
        Cache::rm('redis_client_53');
        return $res ? $code[0] : $code[1];
    }

    //获取53账号密码
    public function get_kf53_key()
    {
        $client_53_id = input('client_53_id');
        $res = Db::table('client_53')->field('Psw_53')->find($client_53_id);
        return ['code' => 0, 'key' => htmlspecialchars_decode($res['Psw_53'])];
    }
}
