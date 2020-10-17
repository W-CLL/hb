<?php

namespace app\target\controller;

use app\common\controller\Common;
use think\Cache;
use think\Db;
use think\Request;
use think\Model;

class Meal extends Common
{
    public function _initialize()
    {
        /* 负责人下拉选项 */
        $this->assign('per', Cache::get("redis_name"));
    }

    public function index()
    {
        return view();
    }

    public function meal()
    {
        return view();
    }
    public function get_meal()
    {
        //接收数据
        $data = input();
        //调用模型获取数据
        $model = model('Meal');
        $result = $model->get_meal($data);
        return $result;
    }

    // 新增订单模块
    public function ins_meal()
    {
        return view();
    }
    public function ins_meal_do()
    {
        //获取数据
        $data = input();
        //创建模型，然后传递数据到模型的对应方法
        $model = model('Meal');
        $result = $model->ins_meal($data);

        //如果数据操作成功返回结果
        if (!$result) {
            return ['code' => 1, 'msg' => '插入失败！请联系管理员！'];
        }
        return ['code' => 0, 'msg' => '添加成功'];
    }

    public function upd_meal()
    {
        $data = input();

        $result = model('Meal')->upd_meal($data);

        $this->assign('list', $result);

        return view();
    }

    public function upd_meal_do()
    {
        //接收数据
        $data['Id'] = input('id');
        $data['User_Id'] = input('user_id');
        $data['Name'] = input('name');
        $data['Lunch'] = input('lunch');
        $data['Dinner'] = input('dinner');
        $data['Remarks'] = input('remarks');

        // 调用模型处理数据
        $result = model('Meal')->upd_meal_do($data);

        if (!$result) {
            return ['code' => 1, 'msg' => '更新失败！请联系管理员！'];
        }
        return ['code' => 0, 'msg' => '更新成功'];
    }

    public function del_meal_do()
    {
        $data['Id'] = input('Id');
        $result = Db::table('meal')->where($data)->delete();
        if(!$result){
            return ['code' => 1, 'msg' => '删除失败！请联系管理员！'];
        }
        return ['code' => 0, 'msg' => '删除成功'];
    }
}
