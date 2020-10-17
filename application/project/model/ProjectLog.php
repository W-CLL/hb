<?php

namespace app\project\model;


use think\Model;

class ProjectLog extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'project_log';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    protected $connection = [
        // 为了存入支持emoji表情单独设置utf8mb4字符集
        'charset' => 'utf8mb4',
    ];
}
