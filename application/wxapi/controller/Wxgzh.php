<?php

namespace app\wxapi\controller;

use app\wxapi\model\Wxgzh as wxgzhModel;
use think\Cache;
use think\Config;
use think\Controller;

/**这个控制器是接收微信传递的消息，然后判断消息类型交给相应模型的方法处理 */
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

        //构造方法用来获取access_token,然后全局保存在缓存中
        if (!Cache::get('Access_Token')||Cache::get('Access_Token')['expire_time']<=time()) {
            $tokenInfo = checkToken(config('appid'), config('appsecret'));
            Cache::set('Access_Token', ['access_token' => $tokenInfo->access_token, 'expire_time' => $tokenInfo->expire_time], $tokenInfo->expire_in);
        }
        $this->access_token = Cache::get('Access_Token')['access_token'];
    }

    //微信公众号接口
    public function index()
    {
        // 获取消息
        $xmldata = file_get_contents("php://input");
        //接受格式正确的XML字符串，并将其作为对象返回。
        $data = simplexml_load_string($xmldata);

        //判断接收的消息类型，根据相应类型交给对应方法处理,对应方法操作成功记得返回true,失败返回false
        if ($data->MsgType == 'text') {
            //文本消息处理
            $result = $this->textMessage($data);
        } elseif ($data->MsgType == 'event') {
            //事件推送处理
            $result = $this->pushEvent($data);
        } else {
            $result = false;
        }
        //操作失败的情况下，无法在5秒内回复微信就直接返回success
        if (!$result) {
            echo 'success';
        }
        exit();
    }

    /**被动回复文本消息处理 */
    private function textMessage($data)
    {
        $model = new wxgzhModel();
        $xml = $model->returnText($data);
        // 回复用户的消息
        header("Content-type: application/xml");
        echo $xml;
        return true;
    }

    /**事件推送消息处理 */
    private function pushEvent($data)
    {
        $result = false;
        file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') . "接收" . $data->Event . "事件\r\n", FILE_APPEND);
        //交给相应事件处理的方法去处理
        if ($data->Event == 'CLICK') {
            //点击事件
            $result = $this->clickEvent($data);
        } elseif ($data->Event == 'subscribe') {
            //扫描带参数二维码事件, 用户未关注时，进行关注后的事件推送
            $result = $this->subscribeEvent($data);
        } elseif ($data->Event == 'SCAN') {
            //扫描带参数二维码事件，用户已关注时的事件推送
            $result = $this->scanEvent($data);
        }
        return $result;
    }

    /**点击事件处理的方法*/
    private function clickEvent($data)
    {
        //处理点击test_temple按键值
        if ($data->EventKey == 'test_temple') {
            $model = new wxgzhModel();
            $result = $model->clickTestTemple($data, $this->access_token);
        }
        return $result;
    }

    /**扫描带参数二维码事件，用户未关注时，进行关注后的事件推送*/
    private function subscribeEvent($data)
    {
        $openid = $data->FromUserName;
        $scene_id = substr($data->EventKey, 8); //获取场景值qrscene_为前缀，后面为二维码的参数值
        $active = Cache::get($scene_id); //场景值相应的缓存中保存这个场景值应该执行的动作如['active'=>'bindwx','uid'=>'98']
        $result = false;

        $model = new wxgzhModel();
        //根据场景值做相关操作
        if ($active['active'] == 'bindwx') {
            // 如果绑定参数存在缓存中//那就绑定微信
            $result = $model->bindwx($this->access_token, $openid, $active['uid']);
        }
        return $result;
    }

    /**扫描带参数二维码事件，用户已关注时的事件推送*/
    private function scanEvent($data)
    {
        $openid = $data->FromUserName;
        $scene_id = $data->EventKey; //创建二维码时的二维码场景值scene_id
        $active = Cache::get($scene_id); //场景值相应的缓存中保存这个场景值应该执行的动作
        $result = false;
        $model = new wxgzhModel();

        //根据场景值判断需要执行的操作
        if ($active['active'] == 'login') {
            //扫码登录
            $result = $model->login($scene_id, $openid);
        } elseif ($active['active'] == 'bindwx') {
            //绑定微信的操作
            $result = $model->bindwx($this->access_token, $openid, $active['uid']);
        }
        return $result;
    }
}
