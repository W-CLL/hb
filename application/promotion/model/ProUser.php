<?php
namespace app\promotion\model;

use think\Model;

 class ProUser extends Model
{
    /**
     * 获取推广账号下的所有消费信息
     */
  public function con(){
return $this->hasOne('PromotionCon',"Pro_Id");
  }
  /**
   * 获取推广账号下的所有充值信息
   */
  public function rec(){
 return $this->hasOne('PromotionRec',"Pro_Id");
  }

}