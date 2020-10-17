<?php
namespace app\client_rec\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\common\controller\Common;
use think\Cache;
class ClientRec extends Common
{
    public function _initialize(){
        /* 客户下拉选项 */
        $this->assign('cli',Cache::get("redis_client"));
        /* 项目名下拉选项 */
        $this->assign('pro',Cache::get('redis_project'));


    }
    //客户打款记录管理
    public function  client_rec(){
        return view();
    }
    //客户打款记录数据
    public function get_client_rec(){
        $limit=input('limit');
        if (session('auth')>2){
            $data['Client_Id']=session('id');
        }
        if (input('sel_project_name')){
            $data['Project_Name']=input('sel_project_name');
        }
        if (input('sel_client_id')){
            $data['Client_Id']=input('sel_client_id');
        }
        if(input('sel_time')){
            $time=input('sel_time');
            $time=explode(' - ',$time);
            $StartTime=strtotime($time[0]);
            $EndTime=strtotime($time[1])+86399;
            $data['Cre_time']=['between',[$StartTime,$EndTime]];
        }
        $list=Db::table('client_rec cr')
            ->join('user_info ui','cr.Client_Id=ui.User_Id')
            ->where($data)
            ->field('cr.Id,from_unixtime(Cre_time) Cre_time,from_unixtime(Suc_time) Suc_time,Name,Project_Name,Money,Remarks')
            ->paginate($limit);
        $list=$list->toArray();
        $res['data']=$list['data'];
        $res['code']=0;
        $res['count'] = $list['total'];
        return $res;
    }
    //新增客户打款记录
    public function ins_client_rec(){
        return view();
    }
    //新增客户打款记录操作
    public function ins_client_rec_do(){
        $code=[['code'=>0,'msg'=>'新增成功！'],['code'=>1,'msg'=>'新增失败！请联系管理员！']];
        $data['Client_Id']=input('client_id');
        $data['Project_Name']=input('project_name');
        $data['Money']=input('money');
        $data['Suc_time']=strtotime(input('suc_time'));
        $data['Cre_time']=time();
        $data['Remarks']=input('remarks');
        $res=Db::table('client_rec')
            ->insert($data);
        return $res?$code[0]:$code[1];

    }
    //删除客户打款记录操作
    public function dels_client_rec_do(){
        $code=[['code'=>0,'msg'=>'删除成功！'],['code'=>1,'msg'=>'删除失败！请联系管理员！']];
        $Id=input('Id');
        $res=Db::table('client_rec')
            ->where('Id',$Id)
            ->delete();
        return $res?$code[0]:$code[1];
    }
    //修改客户打款记录
    public function upd_client_rec(){
        $Id=input('Id');
        $list=Db::table('client_rec cr')
            ->join('user_info ui','cr.Client_Id=ui.User_Id')
            ->where('cr.Id',$Id)
            ->field('cr.Id,from_unixtime(Cre_time) Cre_time,from_unixtime(Suc_time) Suc_time,Name,Project_Name,Money,Remarks')
            ->find();
        $this->assign('list',$list);
        return view();
    }
    //修改客户打款记录操作
    public function upd_client_rec_do(){
    $code=[['code'=>0,'msg'=>'修改成功！'],['code'=>1,'msg'=>'修改失败！请联系管理员！']];
    $Id=input('id');
    $data['Money']=input('money');
    $data['Suc_time']=strtotime(input('suc_time'));
    $data['Cre_time']=time();
    $data['Remarks']=input('remarks');
    $res=Db::table('client_rec')
        ->where('Id',$Id)
        ->update($data);
    return $res?$code[0]:$code[1];
    }
    //客户余额统计
    public function client_sum(){
     return view();
    }
    //客户余额统计
    public function get_client_sum(){
        if (input('sel_client_id')){
        $data['User_Id']=input('sel_client_id');
        }
        if (session('auth')>2){
            $data['User_Id']=session('id');
        }
        //先构造子查询  得到展示条件的总消费跟客户ID
        $Cli_Con=Db::table('promotion_con')
            ->where('Cli_Status',1)
            ->field('Client_Id,Sum(Cli_Money_Con) Cli_Money_Con')
            ->group('Client_Id')
            ->buildSql();

    $list=Db::table('user_info ui')
        ->join('client_rec cr','cr.Client_Id=ui.User_Id','left')
        ->join($Cli_Con.' pc','ui.User_Id=pc.Client_Id','left')
        ->field('User_Id,Name,Sum(Money) Money,Cli_Money_Con')
        ->group('ui.User_Id')
        ->where($data)
        ->where('ui.Type_Id',5)
        ->order('Money desc')
        ->select();
    $res=$list;
   foreach ($res as $k =>$v){
       //如果null为赋值0.00
        if ($v['Money']==null){
            $res[$k]['Money']=number_format(0,2);
        }
        if ($v['Cli_Money_Con']==null){
            $res[$k]['Cli_Money_Con']=number_format(0,2);
        }
       //$res[$k]['Sum']=$v['Money']-$v['Cli_Money_Con'];
       $res[$k]['Sum']=number_format($v['Money']-$v['Cli_Money_Con'],2,'.','');
    }
    $return['code']=0;
    $return['data']=$res;
    return $return;
    }

}

?>