<?php
namespace app\client\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\paginator\driver\Bootstrap;

class Client extends Controller
{

    public function client(Request $request)
    {
        $list = db('client')->select();
        $this->assign('list', $list);
        return view();
    }
}

?>