<?php

namespace app\push\controller;

// require_once VENDOR_PATH . 'web-msg-sender' . DS . 'vendor' . DS . 'autoload.php';

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use think\Cache;
use think\Db;

/**

 * 推送事件
 * 典型调用方式：
 * $push = new PushEvent();
 * $push->setUser($user_id)->setContent($string)->push();
 *
 * Class PushEvent
 * @package app\lib\event
 */

class PushEvent
{
    /**     
     * @var string 目标用户id     
     */
    protected $to_user = '';

    /**
     * @var string 推送服务地址
     */
    protected $push_api_url = 'http://127.0.0.1:2121/';

    /**
     * @var string 推送内容
     */
    protected $content = '';

    /**
     * 设置推送用户，若参数留空则推送到所有在线用户
     *
     * @param string $user
     * @return $this
     */
    public function setUser($user = '')
    {
        $this->to_user = $user ?: '';
        return $this;
    }

    /**
     * 设置推送内容
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content = '')
    {
        $this->content = $content;
        return $this;
    }
    /**

     * 获得要推送的管理层
     *
     * @param string $content
     * @return $this
     */
    public function getAdminCache()
    {
        if (Cache::get('AdminCache')) {
            $res = Cache::get('AdminCache');
        } else {
            $res = Db::table('user u')
            ->join('user_info ui', 'u.Id=ui.User_Id')
            ->where('Status', 1)
            ->where('Type_Id', '<', '5')
            ->where('Type_Id','>','1')
            ->field('u.Id')
            ->select();
            Cache::set('AdminCache', $res, 3600);
            $res = Cache::get('AdminCache');
        }
        return $res;
    }

    /**
     * 推送
     */
    public function push()
    {
        $data = ['type' => 'publish',    'content' => $this->content,    'to' => $this->to_user,];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->push_api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $res = curl_exec($ch);
        curl_close($ch); //dump($res);
    }

    /**
     * 菜单左上角小红点提醒,也可以用来推送数组
     * web_msg_sender扩展start_io.php中自定义的事件类型remind
     */
    public function remind()
    {
        $data = ['type' => 'remind',    'content' => $this->content,    'to' => $this->to_user,];
        $res = curlOpen($this->push_api_url, array('post' => $data));
        return $res;
    }
}
