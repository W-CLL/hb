<?php

namespace app\kh_info\controller;

use app\common\controller\Common;
use think\Db;

class KhTalk extends Common
{
    public function kh_talk()
    {
        return view();
    }

    public function get_kh_talk()
    {
        $limit = input('limit');
        $search = null;
        //查询访客参数条件
        if (input('sel_guest_id')) {
            $search['guest_id'] = ['like', "%" . input('sel_guest_id') . "%"];
        }
        if (input('sel_worker_id')) {
            $search['worker_id'] = ['like', "%" . input('sel_worker_id') . "%"];
        }
        if (input('sel_talk_page')) {
            $search['land_page'] = ['like', "%" . input('sel_talk_page') . "%"];
        }
        //查询时间范围
        $start_time = input('sel_start_time');
        $end_time = input('sel_end_time');
        if ($start_time) {
            $search['talk_time'] = ['>=', date('YmdHis', strtotime($start_time))];
        }
        if ($end_time) {
            $search['end_time'] = ['<=', date('YmdHis', strtotime($end_time))];
        }

        if (input('sel_is_talk') == '0' || input('sel_is_talk') == '1') {
            $search['isTalk'] = input('sel_is_talk');
        }
        if (input('sel_is_mobile') == '0' || input('sel_is_mobile') == '1') {
            $search['isMobile'] = input('sel_is_mobile');
        }
        if (input('sel_is_relink') == '0' || input('sel_is_relink') == '1') {
            $search['relink'] = input('sel_is_relink');
        }

        if (empty($search)) {
            $data = Db::table('kh_talk')->order('talk_time', 'desc')->paginate($limit);
        } else {
            $data = Db::table('kh_talk')->where($search)->order('talk_time', 'desc')->paginate($limit);
            // var_dump(Db::getLastSql());
        }
        $data = $data->toArray();

        //处理查询到的数据
        foreach ($data['data'] as $k => $v) {
            //格式化时间
            $data['data'][$k]['talk_time'] = date('Y-m-d H:i:s', strtotime($v['talk_time']));
            $data['data'][$k]['end_time'] = date('Y-m-d H:i:s', strtotime($v['end_time']));
            // 使用设备为手机时为2其他为1
            switch ($v['device']) {
                case '1':
                    $data['data'][$k]['device'] = 'PC';
                    break;
                case '2':
                    $data['data'][$k]['device'] = '手机';
                    break;
                case '3':
                    $data['data'][$k]['device'] = '微信';
                    break;
                default:
                    $data['data'][$k]['device'] = '其他';
            }
            //对话类型 1，留言；2，机器人；3，流失；4，图标；5，邀请框；6，其他
            switch ($v['talk_type']) {
                case '1':
                    $data['data'][$k]['talk_type'] = '留言';
                    break;
                case '2':
                    $data['data'][$k]['talk_type'] = '机器人';
                    break;
                case '3':
                    $data['data'][$k]['talk_type'] = '流失';
                    break;
                case '4':
                    $data['data'][$k]['talk_type'] = '图标';
                    break;
                case '5':
                    $data['data'][$k]['talk_type'] = '邀请框';
                    break;
                default:
                    $data['data'][$k]['talk_type'] = '其他';
            }
            //解码URL
            $data['data'][$k]['referer'] = urldecode($v['referer']);
            $data['data'][$k]['land_page'] = urldecode($v['land_page']);
            $data['data'][$k]['talk_page'] = urldecode($v['talk_page']);
        }
        $res['code'] = 0;
        $res['count'] = $data['total'];
        $res['data'] = $data['data'];
        return $res;
    }

    public function view_kh_talk()
    {
        $id = input('talk_id');
        $talk_time = strtotime(input('talk_time'));
        if (date('Ymd', $talk_time) < '20200527') {
            //从数据库中得到对话的消息内容
            $data = Db::table('kh_message')->where('talk_id', $id)->select();
        } else {
            //从文件中得到对话的消息内容
            $filename = RUNTIME_PATH . 'talk_message' . DS . date('Ym', $talk_time) . DS . date('d', $talk_time) . DS . $id . '.json';
            if (!is_file($filename)) {
                return view();
            }
            $data = json_decode(file_get_contents($filename), true);
            if (empty($data)) {
                return view();
            }
        }
        // var_dump($data);
        foreach ($data as $k => $v) {
            $data[$k]['msg_time'] = date('Y-m-d H:i:s', strtotime($v['msg_time']));
            switch ($data[$k]['msg_type']) {
                case 'p':
                    $data[$k]['msg_type'] = '客服';
                    break;
                case 'g':
                    $data[$k]['msg_type'] = '访客';
                    break;
                case 's':
                    $data[$k]['msg_type'] = '系统';
                    break;
                case 't':
                    $data[$k]['msg_type'] = '转接';
                    break;
                case 'd':
                    $data[$k]['msg_type'] = '撤回消息';
                    break;
            }
        }
        $this->assign('data', $data);
        return view();
    }

    //删除单个53对话信息
    public function del_kh_talk()
    {
        if (session('type') > 2) {
            return ['code' => 1, 'msg' => '删除失败,你没有权限'];
        }
        $input['Id'] = input('id');
        $input['talk_id'] = input('talk_id');
        if (!empty($input)) {
            $res = Db::table('kh_talk')->where($input)->delete();
            // $res = Db::table('kh_message')->where('talk_id', '=', $input['talk_id'])->delete();
        }
        return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败,请联系管理员'];
    }

    //删除多条53对话信息
    public function dels_kh_talk()
    {
        if (session('type') > 2) {
            return ['code' => 1, 'msg' => '删除失败,你没有权限'];
        }
        $ids = input('ids/a');
        foreach ($ids as $v) {
            $Ids[] = $v['Id'];
        }
        foreach ($ids as $v) {
            $talk_ids[] = $v['talk_id'];
        }
        // var_dump($ids);
        // var_dump($talk_ids);
        if (!empty($ids)) {
            $res = Db::table('kh_talk')->where('Id', 'IN', $Ids)->delete();
            // $res = Db::table('kh_message')->where('talk_id', 'IN', $talk_ids)->delete();
        }
        return $res ? ['code' => 0, 'msg' => '删除成功'] : ['code' => 1, 'msg' => '删除失败,请联系管理员'];
    }
}
