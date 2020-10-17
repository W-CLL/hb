<?php

namespace app\talkdata\model;

use think\Model;

class Data extends Model
{
    //启用自动时间戳
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'Create_time';
    protected $updateTime = 'Update_time';

    public function add_com_data($data)
    {
        // $this->table('com_data')->allowField(true)->save($data);
    }

    public function del($data)
    {
    }
}
