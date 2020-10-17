<?php
namespace app\promotion\model;

use think\Model;
use think\Db;
class PromotionRec extends Model
{
   //项目 去填消费 跳转过来的
   public function ProJ_Rec($data=null)
    {
        if(session('type')!=1){
            $data['r.User_Id']=session('id');
        }
    $list=Db::table('promotion_rec r')
    ->where('r.delete_time',null)
    ->where($data)
    ->join('pro_user a','a.Id=r.Pro_Id')
    ->join('client b','b.Id=r.Client_Id','left')
    ->join('project d','d.Id=r.Project_Id','left')
    ->join('user e','r.User_Id=e.Id','left')
    ->order('Time_Rec desc')
    ->field('r.Id,r.Client_Id,Client,Project_Id,ProjectName,Money_Rec,Time_Rec,name,a.Remarks,Pro_Id,Pro_User');
    return $list;
    }
   public function Ins_ProJ_Rec($PId,$Id)
    {
        $list=Db::table('project a')
        ->join('client c','a.Client_Id=c.Id')
        ->where('a.Id',$PId)
        ->field('a.Id Project_Id,a.Client_Id,ProjectName,c.Client')
        ->find();
    $res=Db::table('pro_user b')
    ->where('b.Id',"in",$Id)
    ->field('Remarks,Id Pro_Id,Pro_User,Pro_Psw')
    ->select();
    //合并
    if ($list&&$res){
    foreach ($res as $k =>$v){
      $add[]=array_merge($res[$k],$list);
    }
    return $add;
    }else{
    return false;
    }
    }
    public function Ins_ProJ_Rec_Do($dataArr){
            // 过滤数据
        foreach ($dataArr as $k => $v) {
            if (!is_array($v)) {
                unset($dataArr[$k]);
            }
        }

        foreach ($dataArr['Money_Rec'] as $k => $v) {

            if ($v != "") {
                $index['Pro_Id'] = $dataArr['Pro_Id'][$k];
                $index['Client_Id'] = $dataArr['Client_Id'][$k];
                $index['Project_Id'] = $dataArr['Project_Id'][$k];
                $index['Money_Rec'] = $dataArr['Money_Rec'][$k];
                $index['Time_Rec'] = date('Y-m-d H:i:s');
                $index['User_Id'] = session('id');
            }
        }
        $list =Db::table('promotion_rec')->insert($index);
        if ($list) {
            return $list;
        } else {
            return false;
        }

    }
    public function Del_Promotion_Con_Do($Id){
         $data['delete_time']=time();
        $list=Db::table('promotion_con')
        ->where('Id',$Id)
        ->update($data);//删除写入时间戳
        return $list;
    }

}