<?php

namespace app\index\controller;

use think\Controller;

use app\common\controller\Common;

use think\Db;

use think\Cache;

class Index extends Common
{

    public function _initialize()
    {
    }

    public function index()
    {
        //获取菜单
        $leftMenu = json_decode(file_get_contents('static/json/leftmenu/basemenu.json'), true);

        if (is_file('static/json/leftmenu/' . session('type') . '.json')) {
            $hidden = json_decode(file_get_contents('static/json/leftmenu/' . session('type') . '.json'), true);

            foreach ($hidden as $k => $v) {
                $leftMenu[$k]['hidden'] = $v['hidden'];
                foreach ($v['list'] as $k2 => $v2) {
                    $leftMenu[$k]['list'][$k2]['hidden'] = $v2['hidden'];
                }
            }
        }

        $this->assign('menu', $leftMenu);
        return view();
    }

    //清除清除redis缓存
    public function clear_redis()
    {
        switch (input('scene')) {
            case 'list':
                Cache::rm('redis_name');
                Cache::rm('redis_client');
                Cache::rm('redis_user_client');
                Cache::rm('redis_project');
                Cache::rm('redis_pro_user');
                Cache::rm('redis_client_53');
                Cache::rm('redis_partner');
                Cache::rm('redis_platform');
                Cache::rm('redis_auth');
                break;
            case 'all':
                Cache::clear();
                break;
        }
        echo '清除成功';
    }
}
