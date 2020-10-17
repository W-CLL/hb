<?php
namespace app\promotion\model;

use think\Model;
use think\Db;
class Client extends Model
{
    public function getStatusAttr($value)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $status[$value];
    }
/*     protected $autoWriteTimestamp=true;
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $createTime = false;     //数据添加的时候，create_time 这个字段自动写入时间戳
    protected $updateTime = false;     //数据更新的时候，update_at 这个字段自动写入时间戳 */
    // 统计账号正常金额
   public function ProJCon()
    {
/*     $res=Db::table('project a')
    ->join('client c','a.Client_Id=c.Id')
    ->field('a.Id,a.ProjectName,c.Client')
    ->buildSql(); */
    $list=Db::table('promotion_con c')
    ->join('pro_user a','a.Id=c.Pro_Id')
    ->join('client b','b.Id=c.Client_Id','left')
    ->join('project d','d.Id=c.Project_Id','left')
    ->order('Time_Con desc')
    ->field('c.Id,c.Client_Id,Client,Project_Id,ProjectName,Money_Con,Time_Con,Rebate_Now,Name,a.Remarks,Pro_Id,Pro_User');
    return $list;
    }
   public function InsProJCon($PId,$Id)
    {
        $list=Db::table('project a')
        ->join('client c','a.Client_Id=c.Id')
        ->where('a.Id',$PId)
        ->field('a.Id Project_Id,a.Client_Id,ProjectName,c.Client')
        ->find();
    $res=Db::table('pro_user b')
    ->where('b.Id',"in",$Id)
    ->field('Remarks,Rebate,Id Pro_Id,Pro_User,Pro_Psw')
    ->select();
    //合并
    foreach ($res as $k =>$v){
      $add[]=array_merge($res[$k],$list);
    }
   // ->field('a.Id,a.Client_Id,Client,ProjectName,Rebate,a.Pro_User_Id,Pro_User,Pro_Psw');
    return $add;
    }
}