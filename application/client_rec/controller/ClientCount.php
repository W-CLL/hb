<?php

namespace app\client_rec\controller;

use app\common\controller\Common;
use think\Cache;
use think\Controller;
use think\Db;

class ClientCount extends Common
{
    public function _initialize()
    {
        /* 客户下拉选项 */
        // $this->assign('cli', Cache::get("redis_client"));
        /* 客户下拉选项 */
        if (session('type') <= 2) {
            $this->assign('cli', Cache::get("redis_client"));
        } else if (session('type') == 5) {
        } else {
            //不是管理员只显示自己负责或有权查看的
            $this->assign('cli', Cache::get("redis_user_client" . session('id')));
        }
        // /* 推广账号下拉选项 */
        // $this->assign('pro_user', Cache::get("redis_pro_user"));
        // /* 53账号下拉选项 */
        // $this->assign('user_53', Cache::get("redis_user_53"));
        // /* 负责人下拉选项 */
        // $this->assign('per', Cache::get("redis_name"));
        // /* 项目名下拉选项 */
        // $this->assign('pro', Cache::get('redis_project'));
        // $this->assign('type', session('type'));
    }

    public function client_count()
    {
        if (session('type') == '5') {
            return "<script>location.href = '/get_client_count/?sel_client_id=" . session('id') . "&view=on'</script>";
        }
        return view();
    }

    public function get_client_count()
    {
        if (input('sel_client_id')) {
            $client = input('sel_client_id');
        } else if (session('type') == 5) {
            $client = session('id');
        } else {
            return '<script>alert("请选择客户");history.back(-1);</script>';
        }
        if (input('sel_time')) {
            $time = input('sel_time');
            $time = explode(' - ', $time);
            $date['start_date'] = $time[0];
            $date['end_date'] = $time[1];
        } else {
            $date['start_date'] = date('Y-m-01');
            $date['end_date'] = date('Y-m-d');
        }

        //客户名
        $clients = Cache::get("redis_client");
        foreach ($clients as $k => $v) {
            if ($v['User_Id'] == $client) {
                $this->assign('client', ['id' => $client, 'name' => $v['Name']]);
            }
        }


        //充值记录
        $rec = Db::table('client_rec')
            ->where([
                'Client_Id' => $client,
                'Suc_time' => ['BETWEEN', [strtotime($date['start_date']), strtotime($date['end_date'])]],
            ])
            ->field('Client_Id,Money,Suc_time Date')->select();

        //项目
        $project = Db::table('project')
            ->where([
                'Client_Id' => $client,
                'Status' => 1,
            ])
            ->field('Id,ProjectName')->select();

        //消费记录
        $con = Db::table('promotion_con')
            ->field('sum(Cli_Money_Con) Sum_Con,sum(Cli_Money_Coin) Sum_Coin,(Sum(Phone)+Sum(Message)) as Sum_Clue, (sum(Cli_Money_Con)/(Sum(Phone)+Sum(Message))) as CueCost, Date,Project_Id')
            ->where([
                'Cli_Status' => 1,
                'Client_Id' => $client,
                'Date' => ['BETWEEN', [$date['start_date'], $date['end_date']]],
            ])
            ->group('Date,Project_Id')
            ->select();


        // 按日期划分消费数据
        $d = [];
        $total = ['con_sum' => [], 'coin_sum' => [], 'clue_sum' => []]; //记录合计数据
        foreach ($con as $k2 => $v2) {
            // 将项目id作为键名，con为具体某一天消费之和
            if (isset($d[$v2['Date']])) {
                $d[$v2['Date']][$v2['Project_Id']] = $v2;
                $d[$v2['Date']]['con'] += $v2['Sum_Con'];
            } else {
                $d[$v2['Date']] = [
                    $v2['Project_Id'] => $v2,
                    'con' => $v2['Sum_Con']
                ];
            }
            //某个项目id的 消费(元)之和,消费（币）之和，线索总数之和
            $total['con_sum'][$v2['Project_Id']] = (isset($total['con_sum'][$v2['Project_Id']]) ? $total['con_sum'][$v2['Project_Id']] : 0) + $v2['Sum_Con'];
            $total['coin_sum'][$v2['Project_Id']] = (isset($total['coin_sum'][$v2['Project_Id']]) ? $total['coin_sum'][$v2['Project_Id']] : 0) + $v2['Sum_Coin'];
            $total['clue_sum'][$v2['Project_Id']] = (isset($total['clue_sum'][$v2['Project_Id']]) ? $total['clue_sum'][$v2['Project_Id']] : 0) + $v2['Sum_Clue'];
        }

        //所有项目的消费之和
        if (!empty($total)) {
            $total['con_sum_all'] = array_sum($total['con_sum']);
        }

        // 按日期划分充值记录
        $d2 = [];
        foreach ($rec as $k => $v) {
            $key = date('Y-m-d', $v['Date']);
            if (isset($d2[$key])) {
                $d2[$key]['rec'] += $v['Money'];
            } else {
                $d2[$key]['rec'] = $v['Money'];
            }
            //充值记录之和
            $total['rec_sum'] = (isset($total['rec_sum']) ? $total['rec_sum'] : 0) + $v['Money'];
        }

        //将充值记录加入到数组中
        $d3 = $d;
        foreach ($d2 as $k => $v) {
            $d3[$k]['rec'] = $v['rec'];
        }
        // 升序排列
        ksort($d3);


        $list['date'] = $d3;
        $list['project'] = $project;
        $list['total'] = $total;
        $list['code'] = 0;

        if (input('view') == 'on') {
            $this->assign($list);
            return view();
        } else {
            return $list;
        }
    }
}
