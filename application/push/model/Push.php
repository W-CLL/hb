<?php

namespace app\push\model;

use think\Cache;
use think\Model;
use think\Request;

class Push extends Model
{
    /**
     * 关联用户信息表
     * @return \think\model\relation\BelongsTo
     */
    public function userInfo()
    {
        //belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
        return $this->belongsTo('userInfo', 'User_Id', 'User_Id');
    }
    /**
     * 关联用户信息表
     * @return \think\model\relation\BelongsTo
     */
    public function clientInfo()
    {
        return $this->belongsTo('userInfo', 'Client_Id', 'User_Id');
    }
    /**
     * 关联53客服电话表，去重复用
     */
    public function kfMoblie()
    {
        return $this->belongsTo('kf_moblie', 'Phone', 'Moblie');
    }
    public function getCretimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
    public function getOktimeAttr($value)
    {
        if ($value) {
            return date('Y-m-d H:i:s', $value);
        }
    }
    /**
     * 获取推送列表
     * @param $limit 分页
     * @param $where 条件
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($limit = 20, $where = null)
    {
        $request = Request::instance();
        $list = $this->with(['client_info', 'user_info'])
            ->order(['Cre_time' => 'desc'])
            ->where($where)
            ->paginate($limit, false, ['query' => $request->request()]);
        $count = $list->total();
        foreach ($list as $k) {
            $k['Client'] = $k->client_info->Name;
            $k['Name'] = $k->user_info->Name;
            // $k['Phone'] = isset($k->kf_moblie->Moblie) ? $k->kf_moblie->Moblie : '';
            //不存在电话号或者这个电话一个月内出现过就算重复
            // if (!empty($k->kf_moblie) && strtotime('+1 month', $k->kf_moblie->Time) > strtotime($k->Cre_time)) {
            //     $k['Being'] = '重复';
            // }
            if (empty($k['Phone'])) {
                $phone = $this->findThePhoneNumbers($k['Content']);
                if($phone){
                    $k['Phone'] = $phone;
                }
            }
            $k = $k->toArray();
            unset($k['kf_moblie']);
            unset($k['client_info']);
            unset($k['user_info']);
            $data[] = $k;
        }
        return ['data' => $data, 'count' => $count, 'code' => 0];
    }

    public function add($data)
    {
        $code = [['code' => 0, 'msg' => '推送成功！'], ['code' => 1, '推送失败！']];
        $res = $this->save($data);
        return $res ? $code[0] : $code[1];
    }
    public function remove($Id)
    {
        $code = [['code' => 0, 'msg' => '删除成功！'], ['code' => 1, '删除失败！']];
        if (session('auth') < 3) {
            $res = $this->delete();
        } else {
            $res = $this->save(['delete_time' => time()]);
        }

        return $res ? $code[0] : $code[1];
    }
    public function ok($Id)
    {
        $data['Status'] = 1;
        $data['Ok_time'] = time();
        $code = [['code' => 0, 'msg' => '确认成功！'], ['code' => 1, 'msg' => '确认失败！']];
        $res = $this->save($data);
        return $res ? $code[0] : $code[1];
    }

    private function findThePhoneNumbers($Str = "")
    {
        header("content-type:text/plain;charset=utf-8");
        // 检测字符串是否为空
        $Str = trim($Str);
        if (empty($Str)) {
            return false;
        }
        // 手机号的获取
        // $reg = "/^1[34578]\d{9}$/"; //匹配数字的正则表达式
        $reg = '/\D1[3-9]\d{9}(\D|$)/';
        preg_match_all($reg, $Str, $result);
        $nums = array();
        $ca = "/^1((34[0-8]\d{7})|((3[0-3|5-9])|(4[5-7|9])|(5[0-3|5-9])|(66)|(7[1-3|5-8])|(8[0-9])|(9[1|8|9]))\d{8})$/";
        foreach ($result[0] as $key => $value) {
            $value = substr($value, 1, 11);
            // var_dump($value);
            if (preg_match($ca, $value)) {
                // $nums[] = array("number" => $value, "type" => "");
                $nums[] = $value;
            } else {
            }
        }
        // 返回最终数组
        return $nums;
    }
}
