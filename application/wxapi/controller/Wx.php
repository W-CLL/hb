<?php

namespace app\wxapi\controller;

use think\Cache;
use think\Config;
use think\Controller;
use think\Db;
use think\Session;
use think\Validate;

/**这个控制器用来做一些，对微信公众号做实际的操作，需要access_token的操作*/
class Wx extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //加载配置
        Config::load(APP_PATH . 'wxapi/config.php');

        //构造方法用来获取access_token,然后全局保存在缓存中
        if (!Cache::get('Access_Token')||Cache::get('Access_Token')['expire_time']<=time()) {
            $tokenInfo = $this->checkToken(config('appid'), config('appsecret'));
            Cache::set('Access_Token', ['access_token' => $tokenInfo->access_token, 'expire_time' => $tokenInfo->expire_time], $tokenInfo->expire_in);
        }
        $this->access_token = Cache::get('Access_Token')['access_token'];
    }
    
    /**
     * 用来检测access_token是否过期，过期会自动获取，
     * 并保存在access_token.php文件，然后返回一个对象，
     * 对象包含三个属性access_token，expires_in,expire_time
     */
    private function checkToken($appid, $secret)
    {
        //从文件中加载access_token的相关数据
        $filename = "./wxapi/access_token.php";
        $json = trim(substr(file_get_contents($filename), 15));
        // var_dump($json);
        $tokenInfo = json_decode($json);
        // var_dump($tokenInfo);返回的是一个对象
    
        // 如果access_token不存在，或者过期了则重新获取
        if (empty($tokenInfo->access_token) || ($tokenInfo->expire_time <= time())) {
            $get_access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            // 调用函数获取access_token
            $result = curlOpen($get_access_token_url, array('ssl' => true));
            if ($result) {
                //先将数据解析为对象，再添加access_token的结束时间记录，然后再保存为json文件
                $tokenInfo = json_decode($result);
                $tokenInfo->expire_time = time() + $tokenInfo->expires_in;
                // var_dump($tokenInfo);
                if (!file_put_contents($filename, "<?php exit();?>" . json_encode($tokenInfo))) {
                    // 保存access_token信息到json配置文件失败
                    return false;
                }
            } else {
                //请求url失败
                return false;
            }
        }
        return $tokenInfo;
    }

    //展示绑定微信的二维码
    public function bindwx()
    {
        //当前用户已经登录，所以可以用session
        $id = Session::get('id');
        //查询绑定信息
        $wxInfo = Db::table('wx_user')->where('User_Id', $id)->find();
        //判断用户是否已经绑定微信
        if (session('openid')||$wxInfo['OpenID']) {
            // 如果有绑定就输出变量，没有就提示绑定
            session('openid',$wxInfo['OpenID']);
            $this->assign('weixin', $wxInfo);
        } else {
            // 设置绑定微信的场景id用于验证，时间600秒有效
            // 关于场景id（用610前缀代表绑定）,缓存设置key为场景值，value为['active'=>'bindwx',其他参数]
            $scene_id = '610' . $id;
            Cache::set($scene_id, ['active' => 'bindwx', 'uid' => $id], 600);
            $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $this->access_token;
            $post = '{"expire_seconds": 600, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": ' . $scene_id . '}}}';
            $res = json_decode(curlOpen($url, array('ssl' => true, 'post' => $post)));

            //返回ticket给前台展示二维码
            $ticket = urlencode($res->ticket);
            $this->assign('ticket', $ticket);
        }
        return view('bindwx');
    }
    
    //解除微信绑定，当前登录用户自主解除
    public function unbindwx()
    {
        $data['User_Id'] = input('userid');
        $data['OpenID'] = input('openid');
        $res = Db::table('wx_user')->where($data)->delete();
        session('openid',null);//解除微信绑定后清除这个session
        return $res ? ['code' => 0, 'msg' => '解绑成功'] : ['code' => 1, 'msg' => '解绑失败，请联系管理员'];
    }

    //微信扫码登录接口
    public function wx_login()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $this->access_token;

        // 设置随机的临时uid用于登录界面提供消息接口，并且也用作登录场景值
        $uid = mt_rand(1, 9999999);
        Cache::set($uid, ['active' => 'login'], 600); //写入缓存
        $post = '{"expire_seconds": 600, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id":' . $uid . '}}}';
        $res = json_decode(curlOpen($url, array('ssl' => true, 'post' => $post)));
        
        //返回ticket给前台展示二维码
        $ticket = urlencode($res->ticket);
        $this->assign('ticket', $ticket);
        $this->assign('uid', $uid);
        return view();
    }
    
    //接收token登录
    public function wx_login_do()
    {
        // 这里接收临时uid,和token,然后和限时缓存中的记录对比,缓存中包含临时token和真实uid
        $token = input('token');
        $uid = input('uid');
        //根据临时uid找到对应缓存，然后对比token,通过则更新真实用户登录数据
        $cache = Cache::get('login_'.$uid);
        if($token != $cache['token']){
            return ['code'=>1,'msg'=>'token错误'];
        }
        //根据真实uid找到对应的用户信息
          $res = Db::table('user a')
            ->where('Id',$cache['uid'])
            ->join('user_info b', 'b.User_Id=a.Id')
            ->field('a.Id,User,Login_time,Ip,a.Status,Name,Type_Id,Auth_Id,Alias')
            ->find();
            
        if(!$res){return ["msg" => "未找到用户信息!，请先登录账号绑定微信", "code" => 2];}
        
         #登录成功
        if ($res['Status'] == '0') {
            return ["msg" => "账号已经被冻结，请联系管理员解封!", "code" => 3];
        }
        #更新登录数据
        $upd['Login_time'] = time();
        $upd['Ip'] = getIp();
        $login = Db::table('user')
            ->where('Id', $res['Id'])
            ->update($upd);
            
        session('id', $res['Id']); // 表示ID
        session('type', $res['Type_Id']); // 账号类型
        if ($res['Type_Id'] == 5) {
            session('username', $res['Alias']); // 用户名
        } else {
            session('username', $res['Name']); // 用户名
        }
        session('auth', $res['Auth_Id']); // 表示权限
        
        session('open_window',1);//初次登录弹出窗口,弹出一次后记得使用session('open_window',null)清除
        //如果绑定了微信也设置上
        $weixin = Db::table('wx_user')->where('User_Id', $res['Id'])->field('OpenID')->find();
        if (isset($weixin['OpenID'])) {
            session('openid', $weixin['OpenID']);
        }
        
        Cache::rm('login_'.$uid);//登录之后记得销毁临时数据
        return ["msg" => "登录成功!", "code" => 0];
    }

    //接收ticket调用微信接口生成二维码图片资源
    public function qrcode()
    {
        $ticket = input('ticket');
        //获取二维码图片
        $qrcode = curlOpen("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket", array('ssl' => true));
        header("Content-Type:image/jpg");
        echo $qrcode;
    }
    
    //接受要推送的内容，发送模板消息
    public function push($post)
    {
       //处理点击时间键值为test_temple的事件，发送模板消息
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $this->access_token;
        $status = json_decode(curlOpen($url, array('post' => $post, 'ssl' => true)));
        if ($status->errcode == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /** 
     * 推送线索消息，使用微信模板消息
     * 
     * @param int $client_id 客户id
     * @param array $clue 线索信息,五个参数array(first,name,phone,weixin,time,remark)
     * 
     *  {{first.DATA}}
     * 
     *  姓名：{{keyword1.DATA}}
     * 
     *  电话：{{keyword2.DATA}}
     * 
     *  微信号：{{keyword3.DATA}}
     * 
     *  时间：{{keyword4.DATA}}
     * 
     *  {{remark.DATA}}
     * 
     */
    public function push_clue($client_id, $clue = array())
    {
        if (empty($clue)) {
            return false;
        }
        //首先根据客户id查找微信openid
        $wx_user = Db::table('wx_user')->where('User_Id', $client_id)->field('OpenID')->find();
        if (!$wx_user) {
            return false;
        }
        $openid = $wx_user['OpenID'];
        $post = "{
            \"touser\": \"$openid\",
            \"template_id\": \"_Tt5kVyuU6iD6AYxaDqW_XUzSYdKOuoC00RjIAs65z0\",
            \"url\": \"" . config('site') . "\",
            \"data\": {
                \"first\": {
                    \"value\":\"" . $clue['first'] . "\",
                    \"color\":\"#173177\"
                },
                \"keyword1\":{
                    \"value\":\"" . $clue['name'] . "\",
                    \"color\":\"#173177\"
                },
                \"keyword2\":{
                    \"value\":\"" . $clue['phone'] . "\",
                    \"color\":\"#173177\"
                },
                \"keyword3\":{
                    \"value\":\"" . $clue['weixin'] . "\",
                    \"color\":\"#173177\"
                },
                \"keyword4\":{
                    \"value\":\"" . $clue['time'] . "\",
                    \"color\":\"#173177\"
                },
                \"remark\":{
                    \"value\":\"" . $clue['remark'] . "\",
                    \"color\":\"#173177\"
                }
            }
        }";
        //推送内容
        return $this->push($post);
    }
    
    /**
     * 推送模板消息,选择指定模板id，推送内容$content，发送给client_id
     * 
     * @param int $client_id 客户id
     * @param string $template_id 模板id
     * @param array $content 推送内容，模板data中的数据
     * @param string $url 详情链接
     */
    public function push_template($client_id,$template_id,$content,$url = '')
    {
        //首先根据客户id查找微信openid
        $wx_user = Db::table('wx_user')->where('User_Id', $client_id)->field('OpenID')->find();
        if (!$wx_user) {
            return ['code'=>1,'msg'=>'推送失败,该用户未绑定微信'];
        }
        $data['touser']=$wx_user['OpenID'];
        $data['template_id']=$template_id;
        $data['url']=$url;
        $data['data']=$content;
        $post = json_encode($data);
        //推送内容，需要access_token的
        return $this->push($post);
    }
    
    //读取消费记录的缓存，并展示
    public function show_cli_promotion()
    {
        $id = $this->request->param('show');
        $data = Cache::get($id);
        if(!$data){
            return '<h1 style="width:100%;">该链接已过期,需要查看请登录系统</h1>';
        }
        $this->assign('list',$data);
        //合计数据
        foreach ($data as $k=>$v){
            $sum['Money_Con'] += $v['Money_Con'];
            $sum['CueSum'] += $v['CueSum'];
        }
        if($v['CueSum']<1){
            $sum['CueCost'] = '0';
        }else{
            $sum['CueCost'] = round($sum['Money_Con']/$sum['CueSum'],2); 
        }
        $this->assign('sum',$sum);
        
        return view();
    }
}
