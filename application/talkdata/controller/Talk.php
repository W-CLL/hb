<?php

namespace app\talkdata\controller;

use app\common\controller\Common;
use think\Cache;
use think\Controller;
use think\Db;
use think\Request;

class Talk extends Common
{
    public function __construct()
    {
        //需要继承父类的构造方法防止覆盖
        parent::__construct();

        //一级分类名称
        if (!Cache::get('redis_talk_cate_1')) {
            $res = Db::table('talk_cate')
                ->where('Parent_Id', '0')
                ->where('Level', '0')
                ->field('Id,Cate_Name,Parent_Id')
                ->order('Id')
                ->select();
            Cache::set('redis_talk_cate_1', $res);
        }
        // 二级分类,标题
        if (!Cache::get('redis_talk_cate_2')) {
            $res = Db::table('talk_cate')
                ->where('Level', '1')
                ->field('Id,Cate_Name,Parent_Id')
                ->order('Id')
                ->select();
            Cache::set('redis_talk_cate_2', $res);
        }
        //展示目录
        if (!Cache::get('redis_talk_cates')) {
            $listdata = '';
            $cate01 = Cache::get('redis_talk_cate_1');
            $cate02 = Cache::get('redis_talk_cate_2');
            foreach ($cate01 as $val) {
                $listdata .= "{title: '" . $val['Cate_Name'] . "',spread: true,id:'" . $val['Id'] . "',children: [";
                foreach ($cate02 as $val_2) {
                    if ($val_2['Parent_Id'] == $val['Id']) {
                        $listdata .= "{title: '" . $val_2['Cate_Name'] . "',id:'" . $val_2['Id'] . "',children: []},";
                    }
                }
                $listdata .= "]},";
            }
            Cache::set('redis_talk_cates', $listdata);
        }
        //一级分类
        $cates01 = Cache::get('redis_talk_cate_1');
        $this->assign('cate01', $cates01);
        //二级分类
        $cates02 = Cache::get('redis_talk_cate_2');
        $this->assign('cate02', $cates02);
        // 分类展示列表
        $listdata = Cache::get('redis_talk_cates');
        $this->assign('listdata', $listdata);
    }
    /** 显示资源列表*/
    public function index()
    {
        return view();
    }

    //添加对话分类
    public function add_talk_cate()
    {
        if (request()->isPost()) {
            if (session('type') > 3) {
                return ['code' => 1, 'msg' => '你没有权限'];
            }
            $result = [['code' => 0, 'msg' => '添加成功'], ['code' => 1, 'msg' => '添加失败，请联系管理员']];
            //接收数据
            $data['Cate_Name'] = input('catename');
            $data['Parent_Id'] = input('parentid');

            if (empty($data['Parent_Id'])) {
                $data['Parent_Id'] = '0';
                $data['Level'] = '0';
            } else {
                $data['Level'] = '1';
            }

            //验证数据
            $validate = validate('Talk');
            if (!$validate->scene('add_cate')->check($data)) {
                return ['code' => 1, 'msg' => $validate->getError()];
            }
            //插入数据
            $res = Db::table('talk_cate')->insert($data);
            //清除缓存
            $this->clearCache();
            return $res ? $result[0] : $result[1];
        }
        return view();
    }

    //编辑对话分类
    public function edit_talk_cate()
    {
        if (!request()->isPost()) {
            return 'request method error';
        }
        if (session('type') > 3) {
            return ['code' => 1, 'msg' => '你没有权限编辑'];
        }
        //接收数据
        $data['Id'] = input('id');
        $data['Cate_Name'] = input('catename');
        //验证数据
        $validate = validate('Talk');
        if (!$validate->scene('edit_cate')->check($data)) {
            return ['code' => 1, 'msg' => $validate->getError()];
        }
        //修改数据
        $res = Db::table('talk_cate')->where('Id', $data['Id'])->update($data);
        if (!$res) {
            return ['code' => 1, 'msg' => '编辑失败请联系管理员'];
        }
        //清除缓存
        $this->clearCache();
        return ['code' => 0, 'msg' => '编辑成功'];
    }

    //删除对话分类
    public function del_talk_cate()
    {
        if (!request()->isPost()) {
            return 'request method error';
        }
        if (session('type') > 2) {
            return ['code' => 1, 'msg' => '你没有权限删除,如需删除请联系管理员'];
        }
        //接收数据
        $data['Id'] = input('id');
        $data['Cate_Name'] = input('catename');
        $Parent_Id = input('parentid');
        //修改数据
        if (!empty($Parent_Id)) {
            //如果Parent_Id不为空表示需要删除的节点是一级分类，需要删除其子节点(即父节点Id为Parent_Id)的相关数据
            $sql = "DELETE talk,talk_cate FROM talk_cate left join talk ON talk.Category_Id=talk_cate.Id WHERE talk_cate.Id=" . $data['Id'] . " OR talk_cate.Parent_Id=" . $Parent_Id;
            $res = Db::execute($sql);
        } else {
            $sql = "DELETE talk,talk_cate FROM talk_cate left join talk ON talk.Category_Id=talk_cate.Id WHERE talk_cate.Id=" . $data['Id'];
            $res = Db::execute($sql);
        }
        if (!$res) {
            return ['code' => 1, 'msg' => '删除失败请联系管理员'];
        }
        //清除缓存
        $this->clearCache();
        return ['code' => 0, 'msg' => '删除成功'];
    }

    //编辑对话
    public function edit_talks()
    {
        if (request()->isPost()) {
            if (session('type') > 3) {
                return ['code' => 1, 'msg' => '你没有权限添加'];
            }
            //接收数据
            $data = input();
            $Category_Id = input('cateid');
            $data = $data['talk'];
            if (empty($data)) {
                return ['code' => 1, 'msg' => '没有数据被提交'];
            }

            // 更新数据
            $count = 0;
            foreach ($data as $k => $v) {
                $res = Db::table('talk')->update($v);
                if ($res) {
                    $count += 1;
                }
            }
            // $model = model('Talk');
            // // $info = $model->table('talk')->where('Category_Id',$Category_Id)->select();
            // $res = $model->saveAll($data);

            if ($count >= 1) {
                return ['code' => 0, 'msg' => '更新"' . $count . '"条对话成功'];
            } else {
                return ['code' => 1, 'msg' => '更新失败，请检查提交的数据'];
            }
        }
        return view('edit_talks');
    }
    public function get_edit_talk()
    {
        //检索标题分类
        if(input('sel_title_id')){
            $search['Category_Id'] = input('sel_title_id');
        }
        //检索时间
        if(input('sel_time')){
            $time = input('sel_time');
            $time = explode(' - ', $time);

            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86400;

            $search['Create_time'] = ['between', [$StartTime, $EndTime]];
        }
        $limit = input('limit');
        //如果有检索条件就检索，没有就检索全部
        if($search){
            $data = Db::table('talk a')
            ->join('talk_cate b', 'a.Category_Id=b.Id')
            ->where($search)
            ->field('a.Id,Category_Id,Cate_Name Title,Parent_Id,Talk_Content,Talk_Type,Create_time')
            ->order('Create_time desc,Id desc')
            ->paginate($limit);
        }else{
            $data = Db::table('talk a')
            ->join('talk_cate b', 'a.Category_Id=b.Id')
            ->field('a.Id,Category_Id,Cate_Name Title,Parent_Id,Talk_Content,Talk_Type,Create_time')
            ->order('Create_time desc,Id desc')
            ->paginate($limit);
        }
        
        $data = $data->toArray();
        foreach ($data['data'] as $k => $v) {
            if ($v['Talk_Type'] == 'g') {
                $data['data'][$k]['Talk_Type'] = '话务';
            } else {
                $data['data'][$k]['Talk_Type'] = '客户';
            }
            $data['data'][$k]['Create_time'] = date('Y-m-d H:i:s', $v['Create_time']);
        }
        $res['data'] = $data['data'];
        $res['count'] = $data['total'];
        $res['code'] = 0;
        return $res;
    }

    //得到某个分类的所有对话内容
    public function get_talks()
    {
        //接收数据
        $data['Id'] = input('cateid');
        $data['Cate_Name'] = input('catename');
        // $talk_cate = Db::table('talk_cate')->where($data)->buildSql();
        //查找数据
        // Db::table('talk')->alias('a')->join('talk_catee b','a.Category_Id=b.Id')->where($data);
        $res = Db::table('talk')->where('Category_Id', $data['Id'])->order('Create_time,Id')->select();
        if (!$res) {
            return ['code' => 1, 'msg' => '查询数据失败'];
        }
        return ['code' => 0, 'msg' => $res];
    }

    //保存多条对话
    public function save_talks()
    {
        $data['Category_Id'] = input('categoruid');
        if (session('type') > 3) {
            return ['code' => 1, 'msg' => '你没有权限'];
        }
        $data = input();
        $data = $data['data'];
        $res = Db::name('talk')->insertAll($data);
        if (!$res) {
            return ['code' => 1, 'msg' => '添加失败，请联系管理员'];
        }
        // var_dump($data);
        return ['code' => 0, 'msg' => '保存成功'];
        // return view();
    }

    //更新单条对话
    public function upd_talk()
    {
        if (session('type') > 3) {
            return '你没有权限编辑';
        }
        //如果是post请求代表更新数据
        if (request()->isPost()) {
            //接受数据
            $data['Id'] = input('id');
            $data['Category_Id'] = input('cateid');
            $data['Talk_Content'] = input('content');
            $data['Talk_Type'] = input('talktype');
            $data['Create_time'] = strtotime(input('createtime'));
            //验证数据
            $validate = validate('Talk');
            if (!$validate->scene('upd_talk')->check($data)) {
                return ['code' => 1, 'msg' => $validate->getError()];
            }
            //更新数据
            $res = Db::table('talk')->update($data);
            if (!$res) {
                return ['code' => 1, 'msg' => '更新失败，请检查输入内容'];
            }
            return ['code' => 0, 'msg' => '更新成功'];
        }
        $data['Id'] = input('Id');
        $res = Db::table('talk')
            ->join('talk_cate', 'talk_cate.Id=talk.Category_Id')
            ->where('talk.Id', $data['Id'])
            ->field('talk.*,Cate_Name')
            ->find();
        $res['Create_time'] = date('Y-m-d H:i:s', $res['Create_time']);
        $this->assign('talk', $res);
        return view();
    }

    //删除单条对话
    public function del_talk()
    {
        if (session('type') > 5) {
            return  ['code' => 1, 'msg' => '你没有权限编辑'];
        }
        $code = [['code' => 0, 'msg' => '删除成功'], ['code' => 1, 'msg' => '删除失败']];
        if (!request()->isPost()) {
            return $code[1];
        }
        $Id = input('Id');
        $res = Db::table('talk')->delete($Id);
        return $res ? $code[0] : $code[1];
    }

    /**清除缓存 */
    private function clearCache()
    {
        Cache::rm('redis_talk_cates');
        Cache::rm('redis_talk_cate_1');
        Cache::rm('redis_talk_cate_2');
    }
}
