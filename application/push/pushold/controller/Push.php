<?php
namespace app\push\controller;
use app\common\controller\Common;
use app\push\controller\PushEvent;
use think\Controller;
use think\Cache;
use think\Db;
use app\push\model\Push as PushModel;
class Push extends Common{
    public function _initialize()
    {
     $this->assign('cli',Cache::get('redis_client'));
    }
    public function index(){
     return view();
    }
    public function read($Id){
      if (input('sel_client_id')){
          $where['Client_Id']=input('sel_client_id');
      }
      if (input('sel_phone')){
          $where['Content']=['like','%'.input('sel_phone').'%'];
      }
        //查询的时间范围
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $StartTime=strtotime($time[0]);
            $EndTime=strtotime($time[1])+86399;
            $where['Cre_time'] = ['between', [$StartTime, $EndTime]];
        }
        if (session('type')==5){
            $where['Client_Id']=session('id');
            $where['delete_time']=null;
        }
     if ($Id<0){return false;}
     $limit=input('limit');
     $model=new PushModel;
     $list=$model->getList($limit,$where);
    return json($list);
    }
    public function save(){
    $data['Client_Id']=input('client_id');
    $data['Kw']=input('kw');
    $data['Content']=input('content');
    $data['User_Id']=session('id');
    $data['Cre_time']=time();
    $model=new PushModel;
    $list=$model->add($data);
    try {
        $push = new PushEvent();
        $push->setUser('400' . input('client_id') . '009')->setContent('您有新的推送！')->push();
    }catch (\Exception $e){
        //出错就写入错误文件
        file_put_contents("PushError.txt",$e,FILE_APPEND);
    }
    return $list;
    }
    public function delete($Id){
       $model=PushModel::get($Id);
       $list=$model->remove($Id);
       return json($list);
    }
    public function update($Id){
       $model=PushModel::get($Id);
       $list=$model->ok($Id);
       return json($list);
    }


}

