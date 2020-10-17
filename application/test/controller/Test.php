<?php

namespace app\test\controller;



use app\common\controller\Common;

use think\Controller;

use think\Db;

use think\Request;

use think\Cache;

use think\Model;

use app\common\controller\Auth;

use app\push\controller\PushEvent;

// class Test extends Common
class Test extends Controller
{

    public function _initialize()
    {

        /*        $aid = 1;

        $auth = new Auth();

        $request = Request::instance();

        $au = $auth->check($request->module() . '/' . $request->controller() . '/' . $request->action(), $aid);

        if (!$au) {// 第一个参数是规则名称,第二个参数是用户UID

          //  return array('status'=>'error','msg'=>'有权限！');

            $this->error('你没有权限');

        }*/
    }

    public function test1()
    {
        return view();
    }

    /**
     * 推送一个字符串
     */
    public function pushAString($msg)
    {
        $user = '400' . session('id') . '009';
        $string = '传输出现了问题';
        $string = input('msg') ? $msg : $string;
        $push = new PushEvent();
        $push->setUser($user)->setContent($string)->push();
    }

    public function test()
    {
        //添加消息提示红点
        // $push = new \app\push\controller\Push();
        // $push->add_dot(session('id'), 'target', '/target');
        // echo strtotime('2020-04-15 14:39:33');

        // 尝试推送
        try {
            $targets = Db::table('target')
                ->where('Status', 1)
                ->where('User_Id', session('id'))
                ->where('End_time', '<', time())
                ->select();

            
            if($targets){
                $push = new PushEvent();
                $content = ['active' => 'target', 'msg' => count($targets).'个目标超过截止时间'];
                $push->setUser('400' . session('id') . '009')->setContent($content)->remind();
            }

        } catch (\Exception $e) {
            //出错就写入错误文件
            file_put_contents("PushError.txt", $e, FILE_APPEND);
        }
    }
}
