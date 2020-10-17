<?php

namespace app\wxapi\controller;

use think\Config;
use think\Session;
use think\Controller;
use think\Db;
use think\Validate;
use think\Cache;
use app\common\controller\Common;
use app\wxapi\model\Gzh as GzhModel;

/**这个控制器继承公共控制器，展示公众号的一些信息，对后台做一些操作*/
class Gzh extends Common
{
    //控制器的初始化方法
    public function _initialize()
    {
        /* 客户下拉选项 */
        $this->assign('cli', Cache::get("redis_client"));

        /* 负责人下拉选项 */
        $this->assign('per', Cache::get("redis_name"));

        /*合作伙伴下拉选项 */
        $this->assign('part', Cache::get("redis_partner"));

        $this->assign('type', session('type'));
    }

    public function gzh()
    {
        return view();
    }

    //得到绑定微信的用户信息
    public function get_user()
    {
        $limit = input('limit');

        if (input('sel_client_id')) {
            $data['a.Client_Id'] = input('sel_client_id');
        }

        if (input('sel_user_id')) {
            $data['a.User_Id'] = input('sel_user_id');
        }

        if (input('sel_status') != null) {
            $data['b.Status'] = input('sel_status');
        }

        $wxuserInfo = Db::table('wx_user')
            ->alias('a')
            ->join('user_info b', 'a.User_Id=b.User_Id')
            //->where($data)
            ->order('a.User_Id')
            ->paginate($limit);

        $wxuserInfo = $wxuserInfo->toArray();

        $res['count'] = $wxuserInfo['total'];
        $res['code'] = 0;
        $res['data'] = $wxuserInfo['data'];

        return $res;
    }

    //推送消费记录模板消息
    public function push()
    {
        if (request()->isPost()) {
            $data = input();
            $data = $data['data'];
            //首先根据客户id查找微信openid
            $wx_user = Db::table('wx_user')->where('User_Id', $data['Client_Id'])->field('OpenID')->find();
            if (!$wx_user) {
                return ['code' => 1, 'msg' => '推送失败,该用户未绑定微信'];
            }
            $openid = $wx_user['OpenID'];
            //需要发送的数据里面不含Id，和client_id
            unset($data['Id']);
            unset($data['Client_Id']);
            $post = "{
              \"touser\": \"$openid\",
              \"template_id\": \"3UDCocSZnfsY1lSjIL8Smzn88K1XnDfrOSkKRUH5j-Q\",
              \"url\": \"http://umbed.site\",
              \"data\": {";
            foreach ($data as $k => $v) {
                $post .= "\"$k\": {
                  \"value\": \"$v\",
                  \"color\": \"#173177\"},";
            }
            //去除最后一个逗号并加上}}
            $post = substr($post, 0, strlen($post) - 1) . "}}";
            //推送内容，需要access_token的交给Wx控制器去做
            $push = new \app\wxapi\controller\Wx();
            return $push->push($post) ? ['code' => 0, 'msg' => '推送成功'] : ['code' => 1, 'msg' => '推送失败'];
        }
    }

    //管理员解除特定用户的微信绑定
    public function unbindwx()
    {
        $data['User_Id'] = input('userid');
        $data['OpenID'] = input('openid');
        $res = Db::table('wx_user')->where($data)->delete();
        return $res ? ['code' => 0, 'msg' => '解绑成功'] : ['code' => 1, 'msg' => '解绑失败，请联系管理员'];
    }
}
