<?php

namespace app\push\controller;

use app\common\controller\Common;
use app\push\controller\PushEvent;
use think\Controller;
use think\Cache;
use think\Db;
use app\push\model\Push as PushModel;

class Push extends Common
{
    public function _initialize()
    {
        $this->assign('cli', Cache::get('redis_client'));
        //  var_dump(Cache::get('redis_client'));
    }
    public function index()
    {
        return view();
    }
    public function read($Id)
    {
        if (input('sel_client_id')) {
            $where['Client_Id'] = input('sel_client_id');
        }
        if (input('sel_phone')) {
            $where['Content'] = ['like', '%' . input('sel_phone') . '%'];
        }
        //查询的时间范围
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;
            $where['Cre_time'] = ['between', [$StartTime, $EndTime]];
        }
        if (session('type') == 5) {
            $where['Client_Id'] = session('id');
            $where['delete_time'] = null;
        }
        if ($Id < 0) {
            return false;
        }
        $limit = input('limit');
        $model = new PushModel;
        $list = $model->getList($limit, $where);
        return json($list);
    }
    public function save()
    {
        $data['Client_Id'] = input('client_id');
        $data['Kw'] = input('kw');
        $data['Content'] = input('content');
        $data['User_Id'] = session('id');
        $data['Cre_time'] = time();
        $model = new PushModel;
        $list = $model->add($data);
        try {
            $push = new PushEvent();
            $push->setUser('400' . input('client_id') . '009')->setContent('您有新的推送！')->push();
            $wx_push = new \app\wxapi\controller\Wx();
            $wx_push->push_clue(input('client_id'), array(
                'first' => '您有新的推送！',
                'name' => ' ',
                'phone' => ' ',
                'weixin' => ' ',
                'time' => date('Y-m-d H:i:s'),
                'remark' => input('content') . '<br>详细内容可以登录后台查看',
            ));
        } catch (\Exception $e) {
            //出错就写入错误文件
            file_put_contents("PushError.txt", $e, FILE_APPEND);
        }
        return $list;
    }
    public function delete($Id)
    {
        $model = PushModel::get($Id);
        $list = $model->remove($Id);
        return json($list);
    }
    public function update($Id)
    {
        $model = PushModel::get($Id);
        $list = $model->ok($Id);
        return json($list);
    }
    public function update_push_remark()
    {
        $Id = input('Id');
        $data['Remarks'] = input('Remarks');
        $code = [['code' => 0, 'msg' => '修改成功！'], ['code' => 1, 'msg' => '修改失败！']];
        if (!request()->isPost()) {
            return $code[1];
        }
        $res = Db::table('push')->where('Id', $Id)->update($data);
        return $res ? $code[0] : $code[1];
    }

    /**
     * 添加红点消息提醒,添加的消息缓存为"dot.$userid",对应单个用户
     * 比如id为98的用户 "dot98"=>[$key=>['active'=>'remind','route'=>'/user','num'=>1]]
     * 
     * @param int $userid 目标用户id
     * @param string $key 识别缓存的键名
     * @param string $route 路由名称,对应主页菜单data-href属性
     */
    public function add_dot($userid = '', $key = '', $route = '')
    {
        //1设置红点提醒的缓存
        $cache = Cache::get('dot' . $userid);
        if (empty($cache[$key])) {
            $cache[$key] = ['active' => 'remind', 'route' => $route, 'num' => '1'];
        } else {
            $cache[$key]['num'] += 1;
        }
        //2新增消息提醒
        $string = $cache[$key];
        $push = new PushEvent();
        //推送给用户
        if ($userid) {
            $user = '400' . $userid . '009';
            $push->setUser($user)->setContent($string)->remind();
        } else {
            return false;
        }
        Cache::set('dot' . $userid, $cache);
        return true;
    }

    /**查看后消除红点 */
    public function rm_dot()
    {
        $route = input('route');
        //默认得到了参数应该是带/的,如/user，需要去除/
        $route = substr($route, 1);
        //清除相应缓存
        $cache = Cache::get('dot' . session('id'));
        unset($cache[$route]);
        Cache::set('dot' . session('id'), $cache);
        if (empty($cache)) {
            Cache::rm('dot' . session('id'));
        }
        return ['code' => 0, 'msg' => '清除成功'];
    }
    /**检查红点设置 */
    public function check_dot()
    {
        $data = Cache::get('dot' . session('id'));
        return ['code' => 0, 'data' => $data];
    }

    public function seturl()
    {
        if (request()->isPost()) {
            $data = input('urls');
            $data = trim(html_entity_decode($data));

            if (json_decode($data)) {
                file_put_contents('./static/json/seturl.json', $data);
            } else {
                return '数据格式错误';
            }
            return '修改成功';
        } else {
            //读取url配置
            $urls = file_get_contents('./static/json/seturl.json');
            $this->assign('url', $urls);
            return view('seturl');
        }
    }
}
