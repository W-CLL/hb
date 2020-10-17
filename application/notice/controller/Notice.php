<?php

namespace app\notice\controller;

use app\common\controller\Auth;
use think\Controller;
use think\Db;
use think\Cache;
use think\Request;
use app\common\controller\Common;
use think\Loader;
use Exception;

class Notice extends Common
{

    public function notice(){
        return view();
    }

}