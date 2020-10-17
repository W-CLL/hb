<?php

namespace app\wxapi\model;

use think\Model;

class Wxgzh extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wx_user';
    
    //事件处理
    public function event($data,$access_token){
        
    }
}