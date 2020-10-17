<?php
namespace app\promotion\model;

use think\Model;
use think\Db;
class PromotionCon extends Model
{
   //项目 去填消费 跳转过来的
   public function ProJ_Con($data=null)
    {
        if(session('type')!=1){
            $data['c.User_Id']=session('id');
        }
    $list=Db::table('promotion_con c')
    ->where('c.delete_time',null)
    ->where($data)
    ->join('pro_user a','a.Id=c.Pro_Id')
    ->join('client b','b.Id=c.Client_Id','left')
    ->join('project d','d.Id=c.Project_Id','left')
    ->join('user e','c.User_Id=e.Id','left')
    ->order('Time_Con desc')
    ->field('c.Id,c.Client_Id,Client,Project_Id,ProjectName,Money_Con,Time_Con,Rebate_Now,name,a.Remarks,Pro_Id,Pro_User');
    return $list;
    }
   public function Ins_ProJ_Con($PId,$Id)
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
    if($list&&$res){
    foreach ($res as $k =>$v){
      $add[]=array_merge($res[$k],$list);
    }
    return $add;
    }else{
        return false;
    }
    }
    public function Ins_ProJ_Con_Do($dataArr){
        // 过滤数据
        foreach ($dataArr as $k => $v) {
            if (!is_array($v)) {
                unset($dataArr[$k]);
            }
        }
        $Id=$dataArr['Project_Id'][0];
        foreach ($dataArr['ConB'] as $k => $v) {
            if ($v != "") {
                $ConB = (float) $dataArr['ConB'][$k];
                $Rebate = (float) $dataArr['Rebate'][$k];
                $index['Pro_Id'] = $dataArr['Pro_Id'][$k];
                $index['Project_Id'] = $dataArr['Project_Id'][$k];
                $index['Client_Id'] = $dataArr['Client_Id'][$k];
                $index['Money_Con'] = (float) sprintf("%.2f", $ConB / $Rebate); // 消费币除返点后保留两位小数四舍五入
                $index['Rebate_Now'] = $dataArr['Rebate'][$k];
                $index['User_Id'] = session('id');
                if ($dataArr['Time_Con'][$k] == "昨天") {
                    $index['Time_Con'] = date("Y-m-d H:i:s", strtotime("-1 day"));
                } elseif ($dataArr['Time_Con'][$k] == "前天")
                $index['Time_Con'] = date("Y-m-d H:i:s", strtotime("-2 day"));
            }
        }
        $list = Db::table('promotion_con')->insert($index);
        return $list;
    }
    public function Del_Promotion_Con_Do($Id){
         $data['delete_time']=time();
        $list=Db::table('promotion_con')
        ->where('Id',$Id)
        ->update($data);//删除写入时间戳
        return $list;
    }

}