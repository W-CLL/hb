<?php
namespace app\push\model;

use think\Cache;
use think\Model;
use think\Request;

class Push extends Model{
    /**
     * 关联用户信息表
     * @return \think\model\relation\BelongsTo
     */
    public function userInfo()
    {
        return $this->belongsTo('userInfo','User_Id','User_Id');
    }
    /**
     * 关联用户信息表
     * @return \think\model\relation\BelongsTo
     */
    public function clientInfo()
    {
        return $this->belongsTo('userInfo','Client_Id','User_Id');
    }
    public function getCretimeAttr($value)
    {
        return date('Y-m-d:H:i:s', $value);
    }
    public function getOktimeAttr($value)
    {
        if ($value){
            return date('Y-m-d:H:i:s', $value);
        }


    }
    /**
     * 获取推送列表
     * @param $limit 分页
     * @param $where 条件
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($limit = 20,$where = null)
    {
        $request = Request::instance();
        $list=$this->with(['client_info','user_info'])->order(['Cre_time' => 'desc'])
            ->where($where)
            ->paginate($limit, false, ['query' => $request->request()]);
        $count=$list->total();
        foreach ($list as $k){
            $k['Client']=$k->client_info->Name;
            $k['Name']=$k->user_info->Name;
            $k=$k->toArray();
            unset($k['client_info']);
            unset($k['user_info']);
            $data[]=$k;
        }
        return ['data'=>$data,'count'=>$count,'code'=>0];

    }

    public function add($data){
        $code=[['code'=>0,'msg'=>'推送成功！'],['code'=>1,'推送失败！']];
        $res=$this->save($data);
        return $res?$code[0]:$code[1];
    }
    public function remove($Id){
        $code=[['code'=>0,'msg'=>'删除成功！'],['code'=>1,'删除失败！']];
        if (session('auth')<3){
            $res=$this->delete();
        }else{
            $res=$this->save(['delete_time'=>time()]);
        }

        return $res?$code[0]:$code[1];
    }
    public function ok($Id){
        $data['Status']=1;
        $data['Ok_time']=time();
        $code=[['code'=>0,'msg'=>'确认成功！'],['code'=>1,'msg'=>'确认失败！']];
        $res=$this->save($data);
        return $res?$code[0]:$code[1];
    }
}