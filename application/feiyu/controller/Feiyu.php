<?php

namespace app\feiyu\controller;

use think\Db;
use think\Cache;
use app\common\controller\Common;
// use app\push\model\Push as PushModel;
use app\feiyu\model\Feiyu as feiyuModel;
use lib\FeiYu as FeiYuCRM;

class Feiyu extends Common
{
    public function _initialize()
    {
        if (session('type') <= 2) {
            /* 客户下拉选项 */
            $this->assign('cli', Cache::get("redis_client"));
        } else {
            //不是管理员只显示自己负责或有权查看的客户
            $this->assign('cli', Cache::get("redis_user_client" . session('id')));
        }
    }

    //飞鱼线索数据模块
    public function clues()
    {
        return view();
    }

    //获取线索数据
    public function get_clues()
    {
        $limit = input('limit');
        $where = [];
        //查询条件
        if (input('sel_client_id')) {
            $where['fu.Client_Id'] = input('sel_client_id');
        }
        if (input('sel_phone')) {
            $where['telphone'] = ['like', '%' . input('sel_phone') . '%'];
        }
        if (input('sel_clue_type') || input('sel_clue_type') === '0') {
            $where['clue_type'] = input('sel_clue_type');
        }
        if (input('sel_name')) {
            $where['fu.Name'] =  ['like', '%' . input('sel_name') . '%'];
        }
        //查询的时间范围
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $StartTime = strtotime($time[0]);
            $EndTime = strtotime($time[1]) + 86399;
            $where['create_time'] = ['between', [$StartTime, $EndTime]];
        }

        if (session('type') == 5) {
            //如果是客户只显示自己的
            $search['Client_Id'] = session('id');
            $fu_ids = Db::table('feiyu_user')->field('Id')->where($search)->select();
            foreach ($fu_ids as $k => $v) {
                $ids[] = $v['Id'];
            }
            $where['fc.client_id'] = ['in', $ids];
            $where['delete_time'] = null;
        } elseif (session('type') > 2 && session('type') < 5) {
            //如果是不是管理员只显示自己的有权查看的
            $search['Pro_Id'] = ['in', Pro_User_Id()];
            $fu_ids = Db::table('feiyu_user')->field('Id')->where($search)->select();
            foreach ($fu_ids as $k => $v) {
                $ids[] = $v['Id'];
            }
            $where['fc.client_id'] = ['in', $ids];
        }

        //调用模型提供数据
        $model = new feiyuModel();
        $data = $model->get_clues($where, $limit);

        $list['code'] = 0;
        $list['count'] = $data['count'];
        $list['data'] = $data['data'];
        return $list;
    }

    //设置对某一个具体用户隐藏飞鱼线索列表字段
    public function sethidefield()
    {
        if (request()->isGet() && !request()->isAjax()) {
            // $a = Cache::get('redis_name');
            // $b = Cache::get('redis_client');
            // $per = array_merge($a, $b);
            // $this->assign('per', $per);
            return view('setfeiyuhidefield');
        }
        if (request()->isAjax()) {
            $json = file_get_contents('./static/json/setFeiyuHideField.json');
            return json($json, 200, ['Content-typr' => 'application/json']);
        }
        if (request()->isPost()) {
            $json = htmlspecialchars_decode(input('hidefield'));
            if (json_decode($json)) {
                $res = file_put_contents('./static/json/setFeiyuHideField.json', $json);
            }
            if ($res) {
                return '修改成功';
            } else {
                return "数据格式错误";
            }
        }
    }

    //确认提取操作
    public function ok()
    {
        // 如果客户操作只操作自己的
        // if (session('type') == 5) {
        //     $where['client_id'] = session('id');
        // }

        $where['Id'] = input('Id');
        $data['ok_status'] = 1;
        $data['ok_time'] = time();

        //调用模型更新数据
        $model = new feiyuModel();
        $result = $model->save($data, $where);

        return $result ? ['code' => 0, 'msg' => '操作成功'] : ['code' => 1, 'msg' => '操作失败'];
    }

    //删除操作
    public function del()
    {
        $where['Id'] = input('Id');

        $model = new feiyuModel();

        // 如果是客户操作只操作自己的
        if (session('type') >= 5) {
            $where['client_id'] = session('id');
            $data['delete_time'] = time();
            //调用模型更新数据
            $result = $model->save($data, $where);
        } else {
            //调用模型删除数据
            $result = $model->where($where)->delete();
        }

        return $result ? ['code' => 0, 'msg' => '操作成功'] : ['code' => 1, 'msg' => '操作失败'];
    }

    //更新记录
    public function upd_clues()
    {
        if (request()->isGet()) {
            $id = input('Id');
            $res = Db::table('feiyu_clues')->where('Id', $id)->find();
            $this->assign('clue', $res);
            return view();
        } else if (request()->isPost()) {
            $data['Id'] = input('Id');
            $data['name'] = input('name');
            $res = Db::table('feiyu_clues')->update($data);
            return $res ? ['code' => 0, 'msg' => '更新成功'] : ['code' => 1, 'msg' => '更新失败'];
        }
    }

    //手动拉取飞鱼线索数据
    public function feiyu()
    {
        $info['startDate'] = date('Y-m-d');
        $info['endDate'] = date('Y-m-d', strtotime('+1 day'));
        $this->assign('info', $info);

        $where = [];
        if (session('type') == 5) {
            // 客户只看自己的
            $where['fu.Client_Id'] = ['=', session('id')];
        } elseif (session('type') > 2 && session('type') < 5) {
            //如果是不是管理员只显示自己的有权查看的
            $where['fu.Pro_Id'] = ['in', Pro_User_Id()];
        }

        $pro_user = Db::table('feiyu_user')
            ->alias('fu')->field('fu.*,pu.Pro_User')
            ->join('promotion_user pu', 'fu.Pro_Id=pu.Id', 'left')
            ->where($where)
            ->group('fu.Pro_Id')
            ->select();

        $this->assign('pro_user', $pro_user);
        return view();
    }

    public function get_feiyu()
    {
        $input['startDate'] = input('startDate');
        $input['endDate'] = input('endDate');
        $input['page'] = input('page');
        $input['page_size'] = input('page_size');

        $Id = input('id');
        //获取需要的key和token
        $res = Db::table('feiyu_user')->where('Id', $Id)->find();
        $input['key'] = $res['Key'];
        $input['token'] = $res['Token'];

        $feiyuApi = new \app\api\controller\Feiyu();
        // $json = $feiyuApi->pullclues($input);
        $json = $feiyuApi->test($input);

        header('Content-type:application/json');
        $json = json_decode($json, true);
        $json['client_id'] = $res['Id'];
        return json($json, 200, ['Content-type:application/json']);
    }

    //get_feiyu后保存需要的数据
    public function save_clues()
    {
        if (request()->isPost()) {
            $param = input();
            $data = json_decode(htmlspecialchars_decode($param['data']), true);

            if (empty($data)) {
                return ['code' => 2, 'msg' => '未选中记录，插入记录条数0'];
            }

            // var_dump($data);
            foreach ($data as $k => $v) {
                $data[$k]['client_id'] = $param['client_id'];
                $clue_ids[] = $v['clue_id'];
                $clue[$v['clue_id']] = $k;
            }
            //过滤数据
            $list = Db::table('feiyu_clues')->where('clue_id', 'in', $clue_ids)->select();
            if ($list) {
                foreach ($list as $k => $v) {
                    $id = $clue[$v['clue_id']];
                    unset($data[$id]);
                }
            }

            if (empty($data)) {
                return ['code' => 2, 'msg' => '所有记录已存在，插入记录条数0'];
            }

            // var_dump($data);
            //插入过滤后的数据
            $model = new feiyuModel();
            $model->allowField(true)->saveAll($data);
            $len = sizeof($data);

            return ['code' => 0, 'msg' => '插入成功，插入记录数' . $len];
        }
    }

    //回传飞鱼线索
    public function push()
    {
        $input = input();
        $this->assign('data', $input);
        return view();
    }

    //回传飞鱼线索
    public function pushclue()
    {
        $Id = input('Id');
        $client_id = input('client_id');

        $input['clue_id'] = input('clue_id');
        $input['clue_convert_type'] = input('clue_convert_type');

        //获取需要的key和token
        $res = Db::table('feiyu_user')->where('Id', $client_id)->find();
        $input['key'] = $res['Key'];
        $input['token'] = $res['Token'];

        $feiyuApi = new \app\api\controller\Feiyu();
        if ($feiyuApi->pushclue($input)) {
            Db::table('feiyu_clues')->where('Id', $Id)->update(['clue_convert_state' => $input['clue_convert_type']]);
            return ['code' => 0, 'msg' => '回传成功'];
        } else {
            return ['code' => -2, 'msg' => '回传失败'];
        }
    }

    //批量回传飞鱼线索
    public function pushs()
    {
        $request = \think\Request::instance();
        if ($request->isGet()) {
            $where = [];
            if (session('type') == 5) {
                $where['fu.Client_Id'] = ['=', session('id')];
            } elseif (session('type') > 2 && session('type') < 5) {
                //如果是不是管理员只显示自己的有权查看的
                $where['fu.Pro_Id'] = ['in', Pro_User_Id()];
            }

            $pro_user = Db::table('feiyu_user')
                ->alias('fu')->field('fu.*,pu.Pro_User')
                ->join('promotion_user pu', 'fu.Pro_Id=pu.Id', 'left')->group('fu.Pro_Id')
                ->where($where)
                ->select();

            $this->assign('pro_user', $pro_user);
            return view();
        }
        if ($request->isPost()) {
            $data = $request->post();
            $input['clue_convert_type'] = $data['clue_convert_type']; //线索转化类型

            //获取需要的key和token
            $res = Db::table('feiyu_user')->where('Id', $data['id'])->find();
            $input['key'] = $res['Key'];
            $input['token'] = $res['Token'];

            //替换字符串
            $phones = str_replace("\n", ",", $data['telphones']);
            // 找到线索id
            $clues = Db::table('feiyu_clues')
                ->where('client_id', $res['Id'])
                ->where('telphone', 'in', $phones)
                ->field('Id,clue_id,clue_convert_state')
                ->select();

            // 实例化 FeiYu 类并传入初始化参数
            $feiyu = new FeiYuCRM([
                'host' => 'https://feiyu.oceanengine.com',
                'pull_route' => '/crm/v2/openapi/pull-clues/',
                'push_route' => '/crm/v2/openapi/clue/callback/',
                'signature_key' => $input['key'],
                'token' => $input['token'],
            ]);


            $ids = [];
            $error = [];
            $aler = [];
            //飞鱼文档描述不清除，不知道批量上传的格式，只好一条一条上传
            foreach ($clues as $k => $v) {
                if ($v['clue_convert_state']) {
                    $aler[] = $v['Id'];
                }
                // 回传数据方法
                $pushRes = $feiyu->pushData([
                    'clue_id' => $v['clue_id'],
                    'clue_convert_state' => $input['clue_convert_type'],
                ]);
                if ($pushRes) {
                    $Ids[] = $v['Id'];
                } else {
                    $error[] = $v['Id'];
                }
            }
            $upd = Db::table('feiyu_clues')->where('Id', 'in', $Ids)->update(['clue_convert_state' => $input['clue_convert_type']]);
            if ($upd) {
                return [
                    'code' => 0, 'msg' => '回传数据完成',
                    'success' => sizeof($Ids), 'error' => sizeof($error), 'count' => count($clues)
                ];
            } else {
                return ['code' => 1, 'msg' => '回传数据失败' . sizeof($aler) . '条数据已经上传，不需要要重复上传'];
            }
        }
    }

    //插入记录
    public function ins_clue()
    {
        if (request()->isGet()) {
            $where = [];
            if (session('type') > 2) {
                //如果是不是管理员只显示自己的有权查看的
                $where['fu.Pro_Id'] = ['in', Pro_User_Id()];
            }
            $where['fu.status'] = 1;

            $pro_user = Db::table('feiyu_user')
                ->alias('fu')->field('fu.*,pu.Pro_User')
                ->join('promotion_user pu', 'fu.Pro_Id=pu.Id', 'left')
                ->where($where)
                ->group('fu.Pro_Id')
                ->select();

            $this->assign('pro_user', $pro_user);

            return view();
        }
        if (request()->isPost()) {
            $param = input();
            $param['create_time'] = strtotime($param['create_time']);
            $model = new feiyuModel();
            $model->allowField(true)->save($param);
            return ['code' => 0, 'msg' => '插入成功'];
        }
    }
}
