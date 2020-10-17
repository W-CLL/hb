<?php

namespace app\kh_info\model;

use think\Model;

class KhMessage extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'kh_message';

    //定义相对关联
    public function khTalk()
    {
        return $this->belongsTo('KhTalk', 'talk_id', 'Id');
    }
}
