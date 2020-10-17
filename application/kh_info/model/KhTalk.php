<?php

namespace app\kh_info\model;

use think\Model;

class KhTalk extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'kh_talk';

    /**
     * 一次对话可以有多条消息,一对多关联
     */
    public function khMessage()
    {
        return $this->hasMany('khMessage', 'talk_id', 'talk_id');
    }
}
