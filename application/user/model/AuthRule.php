<?php

namespace app\user\model;

use think\Model;

class AuthRule extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'auth_rule';

    //获取器对type字段的类型值进行转换
    public function getTypeAttr($value)
    {
        $type = [1 => '实时认证', 2 => '登录认证'];
        return $type[$value];
    }
}
