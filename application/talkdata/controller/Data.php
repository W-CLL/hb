<?php

namespace app\talkdata\controller;

use app\common\controller\Common;
use think\Controller;
use think\Request;
use think\Db;
use think\Cache;
use think\Validate;

/**
 * 竞品资料
 */

class Data extends Common
{

    public function __construct()
    {
        //需要继承父类的构造方法防止覆盖
        parent::__construct();
        if (!Cache::get('com_data_cate')) {
            $res = Db::table('com_data_cate')->select();
            Cache::set('com_data_cate', $res);
        }
        $this->assign('cate', Cache::get('com_data_cate'));
    }

    //竞品资料主页
    public function com_data()
    {
        $data['Cate_Id'] = input('cateid');
        //如果没有选择分类就默认展示第一个
        if (empty($data['Cate_Id'])) {
            $cates = Cache::get('com_data_cate');
            $data['Cate_Id'] = $cates[0]['Id'];
        }

        // 展示状态，0所有人，1内部，2不展示
        if (session('type') > 3) {
            $data['Status'] = 0;
        } else {
            $data['Status'] = ['in', [0, 1]];
        }

        $res = Db::table('com_data')->where($data)->field('Id,Cate_Id,Title,Img,Label,Brief')->select();
        if (!$res) {
            $this->assign('nodata', '无数据');
        } else {
            //切割标签
            foreach ($res as $k => $v) {
                if (!empty($v['Label'])) {
                    $res[$k]['Label'] =  explode('|', $v['Label']);
                    // var_dump($v["Label"]);
                }
            }
            $this->assign('com_datas', $res);
        }

        //保存留言的json
        $li = json_decode(file_get_contents('message/liuyan.json'), true);
        if (!empty($li)) {
            $li = array_slice(array_reverse($li), 0, 20); //倒序一下,显示前20个
        }
        // var_dump($li);
        $this->assign('li', $li);

        return view();
    }

    //展示竞品资料详情页
    public function show_com_data()
    {
        $data['Id'] = input('id');
        $res = Db::table('com_data')->where($data)->field('Content')->find();
        $this->assign('content', $res['Content']);
        return view();
    }

    //竞品资料管理
    public function manage_com_data()
    {
        if (session('type') <= 4) {
            return view();
        } else {
            return '你没有权限';
        }
        return view();
    }
    
    public function get_manage_com_data()
    {
        //检索状态
        $status = input('sel_status');
        if (!is_null($status) && $status != "") {
            $search['Status'] = $status;
        }
        // 检索分类
        if (input('sel_cate_id')) {
            $search['Cate_Id'] = input('sel_cate_id');
        }
        // 检索时间
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);


            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86400;

            $search['Create_time'] = ['between', [$StartTime, $EndTime]];
        }

        $limit = input('limit');
        $com_data_cate = Db::table('com_data_cate')->buildSql();
        if (empty($search)) {
            $data = Db::table('com_data a')
                ->join($com_data_cate . ' c', 'a.Cate_Id=c.Id', 'left')
                ->field('a.Id,Cate Cate_Name,Cate_Id,Title,Label,Status,Update_time')
                ->order('Create_time desc')
                ->paginate($limit);
        } else {
            $data = Db::table('com_data a')
                ->join($com_data_cate . ' c', 'a.Cate_Id=c.Id', 'left')
                ->where($search)
                ->field('a.Id,Cate Cate_Name,Cate_Id,Title,Label,Status,Update_time')
                ->order('Create_time desc')
                ->paginate($limit);
        }
        // Db::getLastSql();
        $data = $data->toArray();
        // var_dump($data);
        $list = $data['data'];
        foreach ($list as $k => $v) {
            // 格式化日期
            $list[$k]['Update_time'] = date('Y-m-d H:i:s', $v['Update_time']);
            if ($list[$k]['Status'] == '0') {
                $list[$k]['Status'] = '所有人';
            } elseif ($list[$k]['Status'] == '1') {
                $list[$k]['Status'] = '内部';
            } else {
                $list[$k]['Status'] = '不展示';
            }
        }
        $res['data'] = $list;
        $res['code'] = 0;
        // $res['sear'] = $search;
        $res['count'] = $data['total'];
        return $res;
    }

    //添加竞品资料分类
    public function add_com_data_cate()
    {
        if (request()->isPost()) {
            $data['Cate'] = input('cate');
            //验证分类名称非空，最大长度，唯一性
            $validate = new Validate(['Cate|分类名称' => 'require|max:30|unique:com_data_cate']);
            if (!$validate->check($data)) {
                return ['code' => 1, 'msg' => $validate->getError()];
            }
            $res = Db::table('com_data_cate')->insert($data);
            if (!$res) {
                return ['code' => 1, 'msg' => '添加失败，请联系管理员'];
            }
            // 清除缓存
            Cache::rm('com_data_cate');
            return ['code' => 0, 'msg' => '添加成功'];
        }
        return view();
    }

    //删除竞品资料分类
    public function del_com_data_cate()
    {
        if (request()->isPost()) {
            if (session('type') > 2) {
                return ['code' => 1, 'msg' => '你没有权限删除，如需删除请联系管理员'];
            }
            $cateid = input('cateid');

            if (!empty($cateid)) {
                //$sql = "DELETE com_data,com_data_cate FROM com_data left join com_data_cate ON com_data.Cate_Id=com_data_cate.Id WHERE com_data_cate.Id=$cateid";
                //$res = Db::execute($sql);
                //关联删除好麻烦
                $res = Db::table('com_data_cate')->delete($cateid);
                Db::table('com_data')->where('Cate_Id', $cateid)->delete();
            }
            if (!$res) {
                return ['code' => 1, 'msg' => '删除失败请联系管理员'];
            }
            // 清除缓存
            Cache::rm('com_data_cate');
            return ['code' => 0, 'msg' => '删除成功'];
        }
        return view();
    }

    //更新竞品资料分类
    public function upd_com_data_cate()
    {
        $code = [['code' => 0, 'msg' => '更新成功'], ['code' => 1, 'msg' => '更新失败，请联系管理员']];
        if (request()->isPost()) {
            $data['Id'] = input('cateid');
            $data['Cate'] = input('newcatename');
            $res = Db::table('com_data_cate')->update($data);
            // 清除缓存
            Cache::rm('com_data_cate');
            return $res ? $code[0] : $code[1];
        }
        return view();
    }

    //添加竞品资料
    public function add_com_data()
    {
        if (request()->isPost()) {
            //接收数据
            $data['Cate_Id'] = input('cateid');
            $data['Title'] = input('title');
            $data['Img'] = input('img'); //这里接收的img只是图片存放地址
            $data['Label'] = input('label');
            $data['Brief'] = input('brief');
            $data['Content'] = input('content');
            $data['Status'] = input('status');
            $data['Offic'] = input('offic');
            $data['Jump_Site'] = input('jump_site');

            $data['Create_time'] = time();
            $data['Update_time'] = time();

            //插入数据
            $res = Db::table('com_data')->insert($data);
            if (!$res) {
                return ['code' => 1, 'msg' => '添加失败，请检查输入内容'];
            }
            return ['code' => 0, 'msg' => '添加成功'];
        }
        return view();
    }

    //竞品资料删除
    public function del_com_data()
    {
        if (session('type') > 2) {
            return ['code' => 1, 'msg' => '删除失败，你没有权限,如需删除请联系管理员'];
        }

        $data['Id'] = input('Id');
        $res = Db::table('com_data')->where($data)->delete();
        if (!$res) {
            return ['code' => 1, 'msg' => '删除失败，请联系管理员'];
        }
        return ['code' => 0, 'msg' => '删除成功'];
    }

    //更新竞品资料
    public function upd_com_data()
    {
        if (request()->isPost()) {
            //接收数据
            $Id = input('id');
            $data['Cate_Id'] = input('cateid');
            $data['Title'] = input('title');
            $data['Img'] = input('img');
            $data['Label'] = input('label');
            $data['Brief'] = input('brief');
            $data['Content'] = input('content');
            $data['Status'] = input('status');
            $data['Offic'] = input('offic');
            $data['Jump_Site'] = input('jump_site');

            $data['Update_time'] = time();
            $res = Db::table('com_data')->where('Id', $Id)->update($data);
            if (!$res) {
                return ['code' => 1, 'msg' => '编辑失败，请检查更新数据'];
            }
            return ['code' => 0, 'msg' => '更新成功'];
        }
        $data['Id'] = input('Id');
        $res = Db::table('com_data')->where($data)->select();

        $this->assign('com_data', $res[0]);
        return $this->fetch('upd_com_data');
        // return view();
    }

    //标题图片上传
    public function imgupload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $info = $file->validate(['size' => 5242880, 'ext' => 'jpg,png,gif', 'type' => 'image/jpeg,image/png,image/gif'])
                ->rule('date')
                ->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                // 成功上传后 获取上传信息
                return ['code' => 0, 'file' => 'uploads' . DS . $info->getSaveName(), 'msg' => '上传成功'];
            } else {
                // 上传失败获取错误信息
                return ['code' => 1, 'msg' => $file->getError()];
            }
        }
    }

    //保存留言
    public function save_liuyan()
    {
        if(!input('liuyan')){
            return '提交内容为空 <a href="#" onclick="pre_page()">返回上一页</a>
                <script>
                function pre_page(){
                    location.href=document.referrer;
                }
                </script>';
        }
        //读取留言
        $li = json_decode(file_get_contents('message/liuyan.json'), true);
        if (!empty($li)) {
            $len = count($li);
        } else {
            $len = 0;
        }
        $li[$len]['id'] = $len;
        $li[$len]['user'] = session('username');
        $li[$len]['message'] = input('liuyan');
        $li[$len]['data'] =  date('Y-m-d');
        $li[$len]['status'] = false;
        file_put_contents('message/liuyan.json', json_encode($li, JSON_UNESCAPED_UNICODE));
        echo '提交成功<a href="#" onclick="pre_page()">返回上一页</a>
                <script>
                function pre_page(){
                    location.href=document.referrer;
                }
                </script>';
    }
    
    //更新留言状态
    public function upd_liuyan()
    {
        if (session('type') > 4) {
            return json(['code' => 1, 'msg' => '']);
        }
        //读取留言
        $li = json_decode(file_get_contents('message/liuyan.json'), true);
        $id = input('id');
        $li[$id]['status'] = !$li[$id]['status'];
        $li[$id]['data'] =  date('Y-m-d');
        file_put_contents('message/liuyan.json', json_encode($li, JSON_UNESCAPED_UNICODE));
        return json(['code' => 1, 'msg' => '操作成功']);
    }

    //点击弹出跳转地址窗口
    public function jump_site()
    {
        $id = input('id');
        $res = Db::table('com_data')->where('Id',$id)->find();
        $offic = $res['Offic'];
        //如果添加了_blank就是在新页面打开需要解析转义字符串
        if(preg_match('/target=.*_blank/',$offic)){
            $offic = htmlspecialchars_decode($offic);
        }
        return ['Id'=>$id,'offic' =>$offic,'jump_site'=>$res['Jump_Site']];
    }
}
