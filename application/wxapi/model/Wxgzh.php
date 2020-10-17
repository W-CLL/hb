<?php

namespace app\wxapi\model;

use app\push\controller\PushEvent;
use think\Cache;
use think\Model;
use think\Validate;
use think\Db;

class Wxgzh extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wx_user';


    /**被动回复文本消息处理 ,接收消息，返回需要发送的消息*/
    public function returnText($data)
    {
        // 根据文本选择回复对应内容
        if ($data->Content == 'test') {
            $ResponseContent = '发送方OpenID' . $data->FromUserName;
        } elseif ($data->Content == 'getid') {
            $ResponseContent = Cache::get("bindwx");
        } elseif ($data->Content == '绑定后台账号') {
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
        return $xml;
    }

    /**
     * 绑定微信的操作，610$user_id，610是绑定场景值的前缀
     * 先拉取用户信息，然后写入数据库，再返回绑定操作结果
     */
    public function bindwx($access_token, $openid, $user_id)
    {
        //拉取用户信息
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $res = json_decode(curlOpen($url, array('ssl' => true)), true);
        if (!$res['subscribe']) {
            file_put_contents('./log/wxapiLog.txt', date('Y-m-d H:i:s') . "无法拉取用户信息$openid\r\n", FILE_APPEND);
            return false;
        }
        $ins['User_Id'] = $user_id;
        $ins['OpenID'] = $res['openid'];
        $ins['Nickname'] = $res['nickname'];
        $ins['Sex'] = $res['sex'];
        $ins['Province'] = $res['province'];
        $ins['City'] = $res['city'];
        $ins['Remark'] = $res['remark'];
        $ins['Headimgurl'] = $res['headimgurl'];

        //验证字段唯一性
        $validate = new Validate([
            'User_Id|绑定账号' => 'require|unique:wx_user',
            'OpenID|绑定微信' => 'require|unique:wx_user',
        ]);
        //推送事件
        $push = new PushEvent();

        if (!$validate->check($ins)) {
            $push->setUser('400' . $ins['User_Id'] . '009')->setContent($validate->getError())->push();
            return false;
        }
        //保存用户信息
        $res = Db::table('wx_user')->insert($ins);
        Cache::rm("610" . $user_id);//绑定完成后删除缓存
        //保存之后推送给前台
        if ($res) {
            $push->setUser('400' . $ins['User_Id'] . '009')->setContent('绑定成功')->push();
        } else {
            $push->setUser('400' . $ins['User_Id'] . '009')->setContent('绑定失败')->push();
        }
        return true;
    }

    //扫描微信公众号二维码登录
    public function login($scene_id, $openid)
    {
        //找到对应openid的用户信息
        $res = Db::table('wx_user')->where('OpenID', $openid)->find();
        //有找到就说明已经账号绑定了微信号，没有的话就是说虽然已经关注了公众号，但是未绑定后台账号，需要登录后绑定
        if (!$res) {
            $push = new PushEvent();
            $push->setUser('400' . $scene_id . '009')->setContent('未绑定')->push();
            return false;
        }
        // 1.首先生成一个临时的有时效性的token发送给前台，这个token当然是和真正的uid相对应的 //而场景值也用作临时uid
        // 2.前台接收后根据这个临时token请求后台登录，这样就不用session跨设备了
        $info = ['uid' => $res['User_Id'], 'token' => str_rand()];
        Cache::set('login_' . $scene_id, $info, 300);

        $push = new PushEvent();
        $push->setUser('400' . $scene_id . '009')->setContent($info['token'])->push();
        return true;
    }

    /**处理点击 test_temple 事件*/
    public function clickTestTemple($data, $access_token) 
    {
        //处理点击时间键值为test_temple的事件，发送模板消息
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
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
            return true;
        } else {
            return false;
        }
    }
}
