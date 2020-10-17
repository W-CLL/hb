<?php
namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;
class Common extends Model
{
    protected $autoWriteTimestamp=true;
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $createTime = false;     //数据添加的时候，create_time 这个字段自动写入时间戳
    protected $updateTime = false;     //数据更新的时候，update_at 这个字段自动写入时间戳

}