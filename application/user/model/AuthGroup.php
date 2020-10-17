<?php

namespace app\user\model;


use think\model\Merge;

/**
 * 聚合模型
 */
class AuthGroup extends Merge
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'auth_group';

    // 定义关联模型列表
    protected $relationModel = ['UserType'];

    // 定义关联外键
    protected $fk = 'Group_Id';

    protected $mapFields = [
        // 为混淆字段定义映射
        'id' => 'AuthGroup.id',
        'Type_Id' => 'UserType.Type_Id',
        'Type_Name' => 'UserType.Type_Name',
        'Group_Id'  => 'UserType.Group_Id'
    ];

}
