<?php

namespace app\wxapi\controller;

use think\Config;
use think\Controller;
use think\Model;
use think\Db;
use think\Cache;
use think\Session;
use think\Validate;
use app\push\controller\PushEvent;

class Wxgzh extends Controller
{
    //基本配置信息
    protected $access_token = ''; //获取的access_token

    public function __construct()
    {
        parent::__construct();
        //加载配置
        Config::load(APP_PATH . 'wxapi/config.php');
        //接入微信api成为开发者
        // if (checkSignature(config('token'))) {
        //     echo $_GET['echostr'];
        // }
        // exit();

        //构造方法用来获取access_token
        $tokenInfo = checkToken(config('appid'), config('appsecret'));
        if (empty($tokenInfo)) {
            file_put_contents("./log/WxApiError.txt", date('Y-m-d H:i:s') . ' 获取access_token失败' . "\r\n", FILE_APPEND);
        }
        $this->access_token = $tokenInfo->access_token;
    }

    //微信公众号接口
    public function index()
    {
        // 获取消息
        $xmldata = file_get_contents("php://input");
        //接受格式正确的XML字符串，并将其作为对象返回。
        $data = simplexml_load_string($xmldata);
        //判断接收的消息类型
        if ($data->MsgType == 'text') {
            //文本消息处理,交给文本处理的方法
            $this->textMessage($data);
        } elseif ($data->MsgType == 'event') {
            //事件推送处理
            $this->event($data);
        }else{
            echo 'success';
        }
        exit();
    }

    /**被动回复文本消息处理 */
    private function textMessage($data)
    {
        // 根据文本选择回复对应内容
        if ($data->Content == 'test') {
            $ResponseContent = '发送方OpenID' . $data->FromUserName;
        }elseif($data->Content == 'getid'){
            $ResponseContent = Cache::get("bindwx98");
        } 
        elseif ($data->Content == '绑定后台账号') {
            $ResponseContent = "http://s.ykhwzx.cn/wxapi/Wx/binduser?openid=" . $data->FromUserName;
        } else {
            echo 'success';
            exit();
        }
        $ResponseTime = time();

        $xml = "<xml>
        <ToUserName>$data->FromUserName</ToUserName>
        <FromUserName>$data->ToUserName</FromUserName>
        <CreateTime>$ResponseTime</CreateTime>
        <MsgType>text</MsgType>
        <Content>$ResponseContent</Content>
        </xml>";

        // 回复用户的消息
        header("Content-type: application/xml");
        echo $xml;
    }

    /**事件推送消息处理 */
    private function event($data)
    {
        file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') ."接收". $data->Event . "事件\r\n", FILE_APPEND);
        //交给相应事件处理的方法去处理
        if ($data->Event == 'CLICK') {
            //点击事件
            $this->clickEvent($data);
        }elseif($data->Event == 'subscribe'){
            //扫描带参数二维码事件, 用户未关注时，进行关注后的事件推送
            $this->subscribeEvent($data);
        }elseif($data->Event == 'SCAN'){
            //扫描带参数二维码事件，用户已关注时的事件推送
            $this->scanEvent($data); 
        }
    }
    
    
    /**点击事件处理的方法*/
    private function clickEvent($data)
    {
        //处理点击test_temple按键值
        if ($data->EventKey == 'test_temple') {
            //处理点击时间键值为...的事件，发送模板消息
            $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $this->access_token;
            $post = "{
                \"touser\": \"$data->FromUserName\",
                \"template_id\": \"GyK9DZj3lyZBm39BFmgTBSclgeHQlsD4o58fU5ftcCg\",
                \"url\": \"http://www.baidu.com\",
                \"data\": {
                    \"test_name\": {
                        \"value\": \"你的OpenID:$data->FromUserName\",
                        \"color\": \"#173177\"
                    },
                    \"test_content\": {
                        \"value\": \"测试文本内容字段\",
                        \"color\": \"#173177\"
                    },
                    \"test_time\": {
                        \"value\": \"请求创建时间$data->CreateTime\",
                        \"color\": \"#173177\"
                    }
                }
            }";
            $status = json_decode(curlOpen($url, array('post' => $post, 'ssl' => true)));
            if ($status->errcode == 0) {
                file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') . "请求成功\r\n", FILE_APPEND);
            } else {
                file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') . $status->errmsg . " 请求失败\r\n", FILE_APPEND);
            }  
        }
    }
    
    /**扫描带参数二维码事件，用户未关注时，进行关注后的事件推送*/
    private function subscribeEvent($data)
    {
        $ins['OpenID'] = $data->FromUserName;
        //获取场景值//qrscene_为前缀，后面为二维码的参数值
        $scene_id = substr($data->EventKey,8);
        // 如果绑定参数存在缓存中//那就绑定微信
        $bind_id = Cache::get("redis_bindwx_".$scene_id);
        if($bind_id){
            file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') . "绑定参数存在$bind_id\r\n", FILE_APPEND);
            //拉取用户信息
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$this->access_token&openid={$ins['OpenID']}&lang=zh_CN";
            $json = curlOpen($url,array('ssl'=>true));
            $res = json_decode($json,true);
            
            //保存
            file_put_contents('./log/wxapiLog.txt',"$json\r\n", FILE_APPEND);
            
            if(!$res['subscribe']){
                 file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') . "无法拉取用户信息{$ins['OpenID']}\r\n", FILE_APPEND);
                 return false;
            }
            file_put_contents('./log/wxapiLog.txt',"拉取用户信息成功\r\n", FILE_APPEND);
            $ins['User_Id'] = $scene_id;
            $ins['OpenID']=$res['openid'];
            $ins['Nickname'] = $res['nickname'];
            $ins['Sex'] = $res['sex'];
            $ins['Province'] = $res['province'];
            $ins['City'] = $res['city'];
           file_put_contents('./log/wxapiLog.txt',"拉取到数据openid:".$ins['OpenID'].'昵称'.$res['nickname']."\r\n", FILE_APPEND);
           

            //验证字段唯一性
            $validate = new Validate([
                'User_Id|绑定账号' => 'require|unique:wx_user',
                'OpenID|绑定微信' => 'require|unique:wx_user',
                'Nickname|微信昵称'=> 'require'
            ]);
            //推送事件
            $push = new PushEvent();
            
            if (!$validate->check($ins)) {
                $push->setUser('400' . $ins['User_Id'] . '009')->setContent($validate->getError())->push();
            }
             file_put_contents('./log/wxapiLog.txt',"验证通过"."\r\n", FILE_APPEND);
            //保存用户信息
            $res = Db::table('wx_user')->insert($ins);
            file_put_contents('./log/wxapiLog.txt',"保存到数据$res"."\r\n", FILE_APPEND);
            
            //保存之后推送给前台
            if($res){
                $push->setUser('400' . $ins['User_Id'] . '009')->setContent('绑定成功')->push();
            }else{
                $push->setUser('400' . $ins['User_Id'] . '009')->setContent('绑定失败')->push();
            }
        }
    }

    /**扫描带参数二维码事件，用户已关注时的事件推送*/
    private function scanEvent($data)
    {
        $openid = $data->FromUserName;
        //创建二维码时的二维码场景值scene_id
        $scene_id = $data->EventKey;
        file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') . "场景值$scene_id\r\n", FILE_APPEND);
        //根据场景值判断需要执行的操作,场景值前三位109自定义设置为登录场景
        if(substr($scene_id,0,3) == '109'){
            //登录场景值，开始进行登录操作
            //找到对应openid的用户信息
            $res = Db::table('wx_user')->where('OpenID',$openid)->find();
            //有找到就说明已经账号绑定了微信号，没有的话需要登录后绑定
            
            // 1.首先生成一个临时的有时效性的token发送给前台，这个token当然是和真正的uid相对应的 //而场景值也用作临时uid
            // 2.前台接收后根据这个临时token请求后台登录，这样就不用session跨设备了
            $info = ['uid'=>$res['User_Id'],'token'=>str_rand()];
            
            Cache::set('login_'.$scene_id,$info,600);
            
            $cache = Cache::get('login_'.$scene_id);
            file_put_contents('./log/wxapiLog.txt', $cache['uid'].' '.$cache['token']."\r\n", FILE_APPEND);
            
            $push = new PushEvent();
            $push->setUser('400' . $scene_id . '009')->setContent($info['token'])->push();
            echo "success";
            exit();
        }
    }
}
