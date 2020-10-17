<?php

namespace app\customer\controller;

use app\common\controller\Common;
use lib\FileUtil;
use think\Controller;
use think\Cache;
use think\Request;
use think\Db;
use think\lib;

class Bottommenu extends Common
{

    public function bottommenu()
    {
        $request = Request::instance();

        if ($request->isGet()) {
            return view();
        }

        if ($request->isPost()) {
            //读取已有的文件
            $list = file_get_contents('static/bottommenu/list.json');
            $list = json_decode($list, true);
            if ($list == null) {
                return ['code' => 0, 'data' => []];
            }
            foreach ($list as $k => $v) {

                $data[$k] = $v;
                $data[$k]['JSpath'] = htmlspecialchars('<script src="' . $request->domain() . '/static/bottommenu/' . $k . '/bottommenu.js">' . '</script>');
                $data[$k]['uniqid'] = $k;
                $data[$k]['remark'] = $v['remark'];
              //  dump($v['remark']);
            }
            return ['code' => 0, 'data' => $data];
        }
    }

    //新建页面
    public function bottommenu_add()
    {
        $request = Request::instance();

        if ($request->isGet()) {
            $this->assign('cli', Cache::get('redis_client'));
            return view();
        }

        if ($request->isPost()) {
            $param = $request->param();
            // 定义错误消息
            $code = ['code' => 0, 'msg' => '创建成功'];

            // 根据用户id找到用户的外部密钥
            $ekey = Db::table('user_info')->where('User_Id', $param['user_id'])->field('Ekey,Name')->find();
            if (!$ekey) {
                $code['code'] = 403;
                $code['msg'] = '错误，未找到客户信息';
                return json($code);
            }

            $id = uniqid();
            $path = 'static/bottommenu/';

            // 先把基础文件复制到指定目录下
            $fileutil = new FileUtil;
            $fileutil->copyDir($path . 'base', $path . $id, true);

            // 把客户外部密钥写入文件common.js
            $text = "var com_interface = \"{$request->domain()}/api/api/message/key/{$ekey['Ekey']}\";";
            file_put_contents($path . $id . '/images/common.js', $text);

            // 根据参数决定显示的图标
            $urlPath = $request->domain() . '/static/bottommenu/' . $id;
            $content = $this->getHtml($param, $urlPath);
            file_put_contents($path . $id . '/bottommenu.js', $content);

            //将新加的文件写入用于记录的json文件下
            $list = file_get_contents($path . 'list.json');
            $list = json_decode($list, true);

            $list[$id] = [
                'id' => $param['user_id'],
                'name' => $ekey['Name'],
                'param' => $param,
                'remark' => $param['remark']
            ];

            unset($list[$id]['param']['remark']);

            file_put_contents($path . 'list.json', json_encode($list, JSON_UNESCAPED_UNICODE));


            $code['path'] = $urlPath . '/bottommenu.js';
            return json($code);
        }
    }

    //查看
    public function bottommenu_show()
    {
        $request = Request::instance();
        $param = $request->param();
        $this->assign('url', $request->domain() . '/static/bottommenu/' . $param['uniqid'] . '/bottommenu.js');
        return view();
    }

    //删除
    public function bottommenu_del()
    {
        if(session('type')>2){
            return json(['code'=>'1','msg'=>'权限不足，请联系管理员删除']);
        }
        $request = Request::instance();
        $param = $request->param();

        $path = 'static/bottommenu/';

        // 删除指定文件夹
        $fileutil = new FileUtil;
        if (!$fileutil->unlinkDir($path . '/' . $param['uniqid'])) {
            return json(['code' => '204', 'msg' => '删除失败']);
        }

        //将删除的之后的记录写入的json文件下
        $list = file_get_contents($path . 'list.json');
        $list = json_decode($list, true);
        unset($list[$param['uniqid']]);
        file_put_contents($path . 'list.json', json_encode($list, JSON_UNESCAPED_UNICODE));

        return json(['code' => '0', 'msg' => '删除成功']);
    }

    //修改
    public function bottommenu_upd()
    {
        $request = Request::instance();
        $param = $request->param();

        $list = file_get_contents('static/bottommenu/list.json');
        $list = json_decode($list, true);

        if ($request->isGet()) {
            // $this->assign('cli', Cache::get('redis_client'));
            $this->assign('id', $param['id']);
            $this->assign('list', $list[$param['id']]);
            return view();
        }
        if ($request->isPost()) {
            // 定义错误消息
            $code = ['code' => 0, 'msg' => '创建成功'];

            $id = $param['id'];
            $path = 'static/bottommenu/';

            // 根据参数决定显示的图标
            $urlPath = $request->domain() . '/static/bottommenu/' . $id;
            $content = $this->getHtml($param, $urlPath);
            file_put_contents($path . $id . '/bottommenu.js', $content);

            //修改后的记录写入文件
            unset($param['id']);
            $list[$id]['param'] = $param;
            $list[$id]['remark'] = $param['remark'];
            unset($list[$id]['param']['remark']);
            file_put_contents($path . 'list.json', json_encode($list, JSON_UNESCAPED_UNICODE));

            return json($code);
        }
    }

    /**返回生成的底部菜单js文本 */
    protected function getHtml($param, $urlPath)
    {
        $menuName = $param['menu'];
        $li = ['
            +"<li id=\"tz\" style=\"border-left: none;\">"
            +"<a href=\" ' . $urlPath . '/jsq/?url="+url+"\" target=\"view_window\">"
            +"<div style=\"background: url(' . $urlPath . '/images/0f_1.gif) center -6px no-repeat;height: 35px;\"></div>"
            +"<div>' . $menuName[0]['name'] . '</div>"
            +"</a>"
            +"</li>"
        ', '
            +"<li id=\"xz\">"
            +"<a href=\"' . $urlPath . '/down/?url="+url+"\" target=\"view_window\">"
            +"<div style=\"background: url(' . $urlPath . '/images/0f_2.gif) center -6px no-repeat;height: 35px;\">"
            +"</div>"
            +"<div>' . $menuName[1]['name'] . '</div>"
            +"</a>"
            +"</li>"
        ', '
            +"<li id=\"zx\">"
            +"<a href=\"' . $param['code53'] . '\" target=\"view_window\">"
            +"<div style=\"background: url(' . $urlPath . '/images/0f_3.png) center -6px no-repeat;height: 25px;padding-top: 10px;\">"
            +"<div class=\"line\">3</div>"
            +"</div>"
            +"<div>' . $menuName[2]['name'] . '</div>"
            +"</a>"
            +"</li>"
        ', '
            +"<li id=\"ly\">"
            +"<a href=\" ' . $urlPath . '/jsq/?url="+url+"\" target=\"view_window\">"
            +"<div style=\"background: url(' . $urlPath . '/images/0f_4.gif) center -6px no-repeat;height: 35px;\"></div>"
            +"<div id=\"keyword\">' . $menuName[3]['name'] . '</div>"
            +"</a>"
            +"</li>"
        ', '
            +"<li id=\"ph\">"
            +"<a href=\"tel:' . $param['phone'] . '\" target=\"view_window\">"
            +"<div style=\"background: url(' . $urlPath . '/images/0f_5.png) center -6px no-repeat;height: 35px;\"></div>"
            +"<div>' . $menuName[4]['name'] . '</div>"
            +"</a>"
            +"</li>"
        '];

        $html = '"<link rel=\"stylesheet\" href=\"' . $urlPath . '/images/style.css\">"
            +" <footer style=\"height:50px;left:0;\">"
            +" <ul>"
        ';
        foreach ($param['menu'] as $i => $v) {
            // 根据参数决定显示的图标
            if (isset($v['show']) && $v['show'] == 'on') {
                $html = $html . $li[$i];
            }
        }
        $html .= '+"</ul></footer>"';

        $content = "
        var url = document.referrer;
        var html;
        html = $html;
        document.write(html);
        document.write(\"<a class='a1'></a>\");
        var a1 = document.getElementsByClassName('a1')[0];
        a1.setAttribute(\"href\", '$urlPath/jsq/?url=' + url);
        ";
        return $content;
    }
}
