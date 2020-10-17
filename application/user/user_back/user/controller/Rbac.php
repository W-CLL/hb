<?php
namespace app\user\controller;

use AlibabaCloud\Cloudwf\V20170328\DelUmengPagePermission4Root;
use think\Controller;
use think\Db;
use think\Request;
use think\Cache;
use app\common\controller\Common;
class Rbac extends Controller
{

    public function _initialize()
    {
        if (session('id') == null) {
            $this->error("请登录", "login/login/login");
        }
        //不是管理员权限滚蛋
        if (session('auth')>2){
            return ['msg'=>'你没有权限','code'=>1];
        }
    }
    public function rbac()
    {

        return view();
    }
    public function get_rbac(){
        $list = Db::table('user_type ut')
            ->where('Type_Id','>',2)
            ->select();
        $res['code']=0;
        $res['data']=$list;
        return $res;
    }
    public function ins_rbac(){
     return view();
    }
    public function ins_rbac_do(){
        $code=[['code'=>0,'msg'=>'新增成功'],['code'=>1,'msg'=>'新增失败']];
    $data['name']=input('name');
    $data['title']=input('title');
    $res=Db::table('auth_rule')
        ->insert($data);
    return $res?$code[0]:$code[1];
    }

    //编辑账号视图
    public function upd_rbac(){
        $this->assign('Group_Id',input('Group_Id'));
        return view();
    }
    public function get_upd_rbac(){

        $Id=input('Group_Id');
        //左侧全部值
        $LeftValue=Db::table('auth_rule')
            ->field('id,title')
            ->select();
        $list=Db::table('auth_group')
            ->field('rules')
            ->where('id',$Id)
            ->find();
        $list=explode(',',$list['rules']);
        //右侧值
        $RightValue=Db::table('auth_rule')
            ->where('id','in',$list)
            ->field('id')
            ->select();

        foreach ($RightValue as $k =>$v){
            $arr[]=$v['id'];
        }

        return ['value'=>$arr,'data'=>$LeftValue];
    }
    //编辑账号操作
    public function upd_rbac_do()
    {
        $value=input('value');
        $value=substr($value,0,strlen($value)-1);//删除最后一个字符
        $id=input('id');
        $code=array(
            ['code'=>0,'msg'=>'修改成功！'],
            ['code'=>1,'msg'=>'修改错误,请联系管理员！'],
        );
        $res=Db::table('auth_group')
            ->where('id',$id)
            ->update(['rules'=>$value]);
        return $res?$code[0]:$code[1];
    }

}

?>