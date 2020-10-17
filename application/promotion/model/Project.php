<?php
namespace app\promotion\model;

use think\Model;
class Project extends Model
{
/*     protected $autoWriteTimestamp=true;
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $createTime = false;     //数据添加的时候，create_time 这个字段自动写入时间戳
    protected $updateTime = false;     //数据更新的时候，update_at 这个字段自动写入时间戳 */
    // 统计账号正常金额
   public function Project()
    {
        return $this->belongsTo('Client',"Client_Id","Id");
    }
}