<?php

namespace app\wxapi\controller;

use think\Config;
// use app\wxapi\model\JSSDK;
use think\Session;
use think\Controller;
use think\Db;
use think\Validate;
use think\Cache;

class Wx extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //加载配置
        Config::load(APP_PATH . 'wxapi/config.php');
        
        //构造方法用来获取access_token
        $tokenInfo = checkToken(config('appid'), config('appsecret'));
        $this->access_token = $tokenInfo->access_token;
    }
    // 微信网页绑定后台账号
    public function binduser()
    {
        $openid = input('openid');
        $this->assign('openid', $openid);
        return view();
    }
    public function binduser_do()
    {
        $data['User'] = input('username');
        $data['Psw'] = md5("abc" . input('password'));
        #查询用户信息
        $res = Db::table('user')
            ->where($data)
            ->field('Id')
            ->find();
        if ($res == null) {
            #密码或者账号错误空
            return ["msg" => "用户名或密码错误!", "code" => 2];
        } else {
            $ins['User_Id'] = $res['Id'];
            $ins['OpenID'] = input('openid');
            //验证字段唯一性
            $validate = new Validate([
                'User_Id|绑定账号' => 'require|unique:wx_user',
                'OpenID|绑定微信' => 'require|unique:wx_user',
            ]);
            if (!$validate->check($ins)) {
                return ["msg" => $validate->getError(), "code" => 2];
            }
            $res = Db::table('wx_user')->insert($ins);
            return $res ? ["msg" => "绑定成功!", "code" => 0] : ["msg" => "绑定失败!", "code" => 1];
        }
    }

/**
 * 这个是网页授权绑定，好像在这里不太实用的样子
 * 
    //后台绑定微信
    public function bindwx()
    {
        $appid = config('appid');
        $redirect_uri = urlencode(config('site') . 'bwx'); //授权后的回调地址
        $scope = 'snsapi_userinfo'; //弹出授权页面
        $state = session('id'); //识别用户
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
        $this->assign('url', $url);
        return view();
    }
    //后台绑定微信的回调跳转链接
    public function bindwx_do()
    {
        $appid = Config::get('appid');
        $secret = Config::get('appsecret');
        $code = input('code');
        $userid = input('state');
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        //通过code换取网页授权access_token
        $res = json_decode(curlOpen($url, array('ssl' => true)));
        //暂且就这样保存吧，这是网页授权access_token和基础access_token不同
        file_put_contents('./wxapi/access_token_js.php', "<?php exit();?>" . json_encode($res));
        
        //拉取用户信息
        $userinfo = $this->pull_userinfo($res->access_token,$res->openid);
        $ins['User_Id'] = $userid;
        $ins['OpenID'] = $res->openid;
        $ins['Refresh_Token'] = $res->refresh_token;
        $ins['Nickname'] = $userinfo->nickname;
        //可选参数，有就设置，没有就空着
        if(isset($userinfo->sex)){
            $ins['Sex'] = $userinfo->sex;
        }
        if(isset($userinfo->province)){
            $ins['Province'] = $userinfo->province;
        }
        if(isset($userinfo->city)){
            $ins['City'] = $userinfo->city;
        }
        
        //验证字段唯一性
        $validate = new Validate([
            'User_Id|绑定账号' => 'require|unique:wx_user',
            'OpenID|绑定微信' => 'require|unique:wx_user',
        ]);
        if (!$validate->check($ins)) {
            return "<script>alert(" . $validate->getError() . ");</script>";
        }
        //保存用户信息
        $res = Db::table('wx_user')->insert($ins);
        return $res ? view('bindwx_do') : "<script>alert('绑定失败');</script>";
    }
    //网页拉取用户信息
    public function pull_userinfo($access_token,$openid)
    {
        // 拉取用户信息(需scope为 snsapi_userinfo)
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $res = json_decode(curlOpen($url, array('ssl' => true)));
        if($res->openid){
            return $res;
        }else{
            return false;
        }
    }
    
*/

    //展示绑定微信的二维码
    public function bindwx()
    {
        //当前用户已经登录，所以可以用session
        $id = Session::get('id');
        // 设置绑定微信场景id用于验证，时间600秒有效
        Cache::set('redis_bindwx_'.$id, $id,600);
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token;
        $post = '{"expire_seconds": 600, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
        $res = json_decode(curlOpen($url,array('ssl'=>true,'post'=>$post)));
            
        //返回ticket给前台展示二维码
        $ticket = urlencode($res->ticket);
        $this->assign('ticket',$ticket);
        return view('bindwx');
    }
    
        
    //微信扫码登录接口
    public function wx_login()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token;

        // 设置随机的临时uid用于登录界面提供消息接口，并且也用作登录场景值,设置成109这个前缀,看着像log
        $uid = '109'.mt_rand(1,9999);
        $post = '{"expire_seconds": 600, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id":'.$uid.'}}}';
        $res = json_decode(curlOpen($url,array('ssl'=>true,'post'=>$post)));
        //返回ticket给前台展示二维码
        $ticket = urlencode($res->ticket);
        $this->assign('ticket',$ticket);
        $this->assign('uid',$uid);
        return view();
    }
    
    
    //接收ticket调用微信接口生成二维码图片资源
    public function qrcode()
    {
        $ticket = input('ticket');
        //获取二维码图片
        $qrcode = curlOpen("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket",array('ssl'=>true));
        header("Content-Type:image/jpg");
        echo $qrcode;
    }
    

}
