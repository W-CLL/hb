<?php

namespace app\target\model;

use think\Model;

class Meal extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'meal';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'Create_time';
    protected $updateTime = 'Update_time';

    // 新增点餐
    public function ins_meal($data)
    {
        $d['User_Id'] = $data['user_id'];
        $d['Lunch'] = $data['lunch'];
        $d['Dinner'] = $data['dinner'];
        $d['Remarks'] = $data['remarks'];
        //我都点后悔数据库字段要用大写开头啦，这样接收数据好烦

        // 过滤数组中的非数据表字段数据
        $result = $this->allowField(true)->save($d);

        if (!$result) {
            return false;
        }
        return true;
    }

    public function get_meal($data)
    {
        $limit = $data['limit'];

        //如果有检索条件则接收
        if ($data[('sel_user_id')]) {
            $d['a.User_Id'] = $data[('sel_user_id')];
        }
        if ($data['sel_lunch'] != null) {
            $d['Lunch'] = $data['sel_lunch'];
        }
        if ($data['Dinner'] != null) {
            $d['Dinner'] = $data['Dinner'];
        }
        if ($data['sel_time']) { //查询的时间范围
            $time = $data['sel_time'];
            $time = explode(' - ', $time);

            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;

            $d['Create_time'] = ['between', [$StartTime, $EndTime]];
        } else {
            // 默认查询今天的
            $StartTime = strtotime(date('Y-m-d', time()));
            $EndTime = $StartTime + 86399;
            $d['Create_time'] = ['between', [$StartTime, $EndTime]];
        }

        //查询数据
        $result = $this->where($d)
            ->alias('a')
            ->join('user_info b', 'a.User_Id=b.User_Id')
            ->field('a.*,b.Name')
            ->paginate($limit);

        $dataInfo = $result->toArray();
        //有五个键["total","per_page","current_page","last_page","data"]

        foreach ($dataInfo['data'] as $key => $val) {
            //格式化给前台的时间数据
            $dataInfo['data'][$key]['Create_time'] = date('Y-m-d H:i:s', $val['Create_time']);
            $dataInfo['data'][$key]['Update_time'] = date('Y-m-d H:i:s', $val['Update_time']);

            $dataInfo['data'][$key]['Lunch'] = $dataInfo['data'][$key]['Lunch'] ? '需要' : '不需要';
            $dataInfo['data'][$key]['Dinner'] = $dataInfo['data'][$key]['Dinner'] ? '需要' : '不需要';
        }

        $res['count'] = $dataInfo['total']; //全部记录条数
        $res['code'] = 0;
        $res['data'] = $dataInfo['data'];

        return $res;
    }


    public function upd_meal($data)
    {
        $d['Id'] = $data['Id'];

        $result = $this->where($d)
            ->alias('a')
            ->join('user_info b', 'a.User_Id=b.User_Id')
            ->field('a.*,b.Name')
            ->find();

        $res['Id'] = $result->Id;
        $res['User_Id'] = $result->User_Id;
        $res['Name'] = $result->Name;
        $res['Lunch'] = $result->Lunch;
        $res['Dinner'] = $result->Dinner;
        $res['Remarks'] = $result->Remarks;

        return $res;
    }

    //更新点餐数据
    public function upd_meal_do($data)
    {
        $dataInfo = $this->find($data['Id']);
        // 更新数据
        $result = $dataInfo->allowField(true)->save($data);
        if ($result) {
            return true;
        }
        return false;
    }
}
