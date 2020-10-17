<?php

namespace app\Kw\controller;

use app\common\controller\Common;
use think\Controller;
use think\Cache;
use think\Db;

class Kw extends Common
{
    public function _initialize()
    {
        /* 客户下拉选项 */
        $this->assign('cli', Cache::get("redis_client"));
    }

    public function kw()
    {
        return view();
    }
    public function get_kw()
    {
        $list = DB::table('kw')
            ->join('user_info ui', 'ui.User_Id=kw.Client_Id', 'left')
            ->field('kw.*,ui.Name Client')
            ->select();
        foreach ($list as $k => $v) {
            if ($v['Client'] == "") {
                $list[$k]['Client'] = "无";
            }
        }
        $data['code'] = 0;
        $data['data'] = $list;
        return json($data);
    }
    public function ins_kw()
    {
        return view();
    }
    public function ins_kw_do()
    {
        $re_code = array(["code" => 0, "msg" => "添加成功！"], ["code" => 1, "msg" => "添加失败,相关名称已存在!"]);
        $name = input('name');
        //判断账号是否存在
        $res = Db::table('kw')
            ->where('Name', $name)
            ->find();
        if ($res) {
            return json($re_code[1]);
        } else {
            $data['Name'] = $name;
            $data['Client_Id'] = input('client_id');
            $data['Kw'] = input('kw');
            $list = Db::table('kw')->insert($data);
            return json($re_code[0]);
        }
    }
    public function dels_kw()
    {
        $code = array(["code" => 0, "msg" => "删除成功！"], ["code" => 1, "msg" => "删除失败,用户不存在!"]);
        $res = Db::table('kw')->where('Id', input('Id'))->delete();
        return $res ? $code[0] : $code[1];
    }
    public function upd_kw()
    {
        $Id = input('Id');
        $list = Db::table('kw')
            ->join('user_info ui', 'ui.User_Id=kw.Client_Id', 'left')
            ->where('Id', $Id)
            ->field('kw.*,ui.Name Client')
            ->find();
        $this->assign('list', $list);
        return view();
    }
    public function upd_kw_do()
    {
        $code = array(["code" => 0, "msg" => "更新成功！"], ["code" => 1, "msg" => "更新失败,关键字已存在!"], ["code" => 2, "msg" => "更新失败,请输入新数据!"]);
        $Id = input('id');
        $data['Name'] = input('name');
        $data['Client_Id'] = input('client_id');
        $data['Kw'] = input('kw');
        $res = Db::table('kw')
            ->where('Id', $Id)
            ->update($data);
        return $res ? $code[0] : $code[2];
    }

    public function input()
    {
        return view();
    }
    public function urlinput()
    {
        $isMatched = null;
        $val = input('val');
        $list = Db::table('kw')
            ->join('user_info ui', 'kw.Client_Id=ui.User_Id', 'left')
            ->where('Kw', 'not null')
            ->field('kw.*,ui.Name Client,Client_Id')
            ->select();
        foreach ($list as $k => $v) {
            try {
                $kw = $v['Kw'];
                $kw = str_replace("/", "\/", $kw);
                $isMatched = preg_match('/' . $kw . '/', $val, $matches); //匹配一次，中了就别匹配
                //匹配到就终止
            } catch (\Exception $e) {
                dump($e);
                die;
            }
            if ($isMatched) {
                $name = $v['Name'];
                $client = $v['Client'] == '' ? '无' : $v['Client'];
                $client_id = $v['Client_Id'] == '' ? '' : $v['Client_Id'];
                break;
            }
        }

        if ($isMatched == 1) {
            return ['Name' => $name, 'Client' => $client, 'push' => true, 'Client_Id' => $client_id];
        } else {
            return ['Name' => "没有匹配到！", 'Client' => '没有匹配到！', 'push' => false, 'Client_Id' => $client_id];
        }
    }
}
