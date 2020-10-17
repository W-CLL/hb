<?php

namespace app\wx_mini\controller;

include EXTEND_PATH . 'lib/wxBizDataCrypt.php';

use think\Controller;
use think\Request;
use lib\WXBizDataCrypt;


class Index extends Controller
{
    public function _initialize()
    {
        $this->json = \json_decode(file_get_contents("./wxapi/index.json"), true);
        $this->request = Request::instance();
    }
    // 首页
    public function index()
    {
        switch (\input('request')) {
            case 'tabdata':
                return \json($this->json['tabList']);
        }
    }
    //项目列表
    public function project()
    {
        if (input('id')) {
            foreach ($this->json['proList'] as $v) {
                if ($v['id'] == input('id')) {
                    return json(["proList" => [$v]]);
                }
            }
        }
        return \json($this->json['proList']);
    }

    /**  获取用户微信openid  */
    public function get_openid()
    {
        $param = $this->request->param();
        //定义默认返回的数据
        $res = ['code' => 0, 'openid' => '', 'msg' => '获取openid成功'];

        if (empty($param['code'])) {
            $res['code'] = -2;
            $res['msg'] = "code is not empty";
            return json($res);
        }

        $js_code = $param['code'];
        $AppID =  config('appid');
        $AppSecret =  config('appsecret');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$AppID&secret=$AppSecret&js_code=$js_code&grant_type=authorization_code";

        // 调用微信登录接口
        $code2Session = json_decode(curlOpen($url, array('ssl' => true)), true);

        //如果错误代码为不为0就是请求失败
        if ($code2Session['errcode']) {
            $res['code'] = $code2Session['errcode'];
            $res['msg'] = $code2Session['errmsg'];
            return json($res);
        }

        //这里暂时保存了用户的session_key，openid
        session('mini_session_key', $code2Session['session_key']);
        session('mini_openid', $code2Session['openid']);

        $res['openid'] = $code2Session['openid'];
        $res['code'] = 0;

        return json($res);
    }

    /**
     * 登录接口
     */
    public function login()
    {
        $param = $this->request->param();

        //定义默认返回的数据
        $res = ['code' => 0, 'token' => '', 'state' => 'online', 'msg' => '登录状态：在线'];

        if ($param['active'] == 'userinfo') {
            // {
            //     "openId": "OPENID",
            //     "nickName": "NICKNAME",
            //     "gender": GENDER,
            //     "city": "CITY",
            //     "province": "PROVINCE",
            //     "country": "COUNTRY",
            //     "avatarUrl": "AVATARURL",
            //     "unionId": "UNIONID",
            //     "watermark": {
            //       "appid":"APPID",
            //       "timestamp":TIMESTAMP
            //     }
            //   }
            $user = $this->encrypte($param['ncryptedData'], $param['iv']);
            $session = session('?user') ? session('user') : [];
            var_dump($user);
            var_dump($session);
            var_dump(session('session_key'));
            // $result = array_merge($user, $session);
            // session('user', $result);
        } else if ($param['active'] == 'phone') {
            // {
            //      "phoneNumber":"17879529508",
            //      "purePhoneNumber":"17879529508",
            //      "countryCode":"86",
            //      "watermark":{"timestamp":1592368375,"appid":"wx346b47bf7996cfed"}
            // };
            $phone = $this->encrypte($param['ncryptedData'], $param['iv']);

            session('phone',  $phone);
        }
        $res['user'] = session('user');
        $res['phone'] = session('phone');
        $res['token'] = 1;
        return json($res);
    }

    /**检查登录 */
    public function check_log($param, &$res)
    {
        $param = $this->request->param();

        //定义默认返回的数据
        $res = ['code' => 0, 'token' => '', 'state' => 'online', 'msg' => '登录状态：在线'];
        //需要根据token判断登录态
        if (empty($param['token'])) {
            $res['code'] = -2;
            $res['msg'] = "param token cannot empty";
        }
        if (session('user') == null || session('user.token') != $param['token']) {
            session('user', null);
            $res['code'] = -2;
            $res['state'] = 'offline';
            $res['msg'] = '登录状态：离线';
        }
        return json($res);
    }

    /**微信小程序加密数据解密算法 */
    protected function encrypte($encryptedData, $iv)
    {
        $appid = config('appid');
        $sessionKey = session("mini_session_key");

        //实例化微信小程序加密数据解密算法类
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            return json_decode($data, true);
        } else {
            return json_decode($errCode, true);
        }
    }




    //城市选项
    public function wxapp_area_list()
    {
        $json = [['id' => 1, 'p_name' => '广州']];
        return json($json);
    }
    //职位列表
    public function actionPositions()
    {
        $json['code'] = 1;
        $json['ret'] = [];
        $json['ret']['positions'] = [
            [
                'id' => 1, 'enterprise_logo' => request()->domain() . '/static/images/weixing.jpg', 'enterprise_name' => '企业名称',
                'p_name' => '标题', 'p_wages' => '工资', 'p_address' => '工作地址',
                'p_edujy' => '3-5年', 'p_education' => '学历', 'p_addtime' => '2020-06-15',
                'p_desc' => '职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍'
            ], [
                'id' => 2, 'enterprise_logo' => request()->domain() . '/static/images/weixing.jpg', 'enterprise_name' => '企业名称',
                'p_name' => '标题', 'p_wages' => '工资', 'p_address' => '工作地址',
                'p_edujy' => '3-5年', 'p_education' => '学历', 'p_addtime' => '2020-06-15',
                'p_desc' => '职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍，职位描述简要介绍'
            ]
        ];
        return json($json);
    }
}
