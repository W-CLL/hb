<?php

namespace app\index\controller;

use think\Controller;
use app\common\controller\Common;
use think\Db;
use think\Cache;

class Main extends Common
{
    public function main()
    {
        // $projectList = $this->myProject(session('id'));
        // $this->assign($projectList);
        $this->assign('user', Cache::get('redis_name'));
        if (session('type') <= 2) {
            $this->assign('pro', Cache::get('redis_project'));
        } else {
            $pro = Db::table('project')
                ->field('ProjectName,Id')
                ->where(function ($query) {
                    $query->where('User_Id', session('id'))
                        ->whereOr('See_User_Id', 'like', "%@," . session('id') . "@,%");
                    return $query;
                })
                ->where('Status', 1)
                ->select();
            $this->assign('pro', $pro);
        }
        if (session('type') >= 5) {
            return view('main_client');
        } else {
            return view();
        }
    }

    /**
     * 项目图表的数据
     */
    public function get_my_project()
    {
        //默认日期
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $date = [$yesterday, $yesterday];

        //日期范围
        if (input('sel_date')) {
            $date = explode(' - ', input('sel_date'));
        }
        //计算日期天数
        $count_date = date_diff(date_create($date[0]), date_create($date[1]));

        // 获取ProjectName项目名称 和 Target目标成本,Con消费总数，Res线索总数
        if (session('type') <= 2) {
            //管理员查看所有
            $list = Db::table('project p')
                ->join('promotion_con c', 'p.Id=c.Project_Id', 'left')
                ->where('Date', 'between time', $date)
                ->where('p.Status', 1)
                ->field('p.ProjectName ,p.EstimatedCost Target, TargetNumber, Project_Id, Sum(Money_Con) Con, Sum(c.Phone+Message) Res')
                ->group('Project_Id')
                ->select();
        } else {
            $uid = session('id');
            $list = Db::table('project p')
                ->join('promotion_con c', 'p.Id=c.Project_Id', 'left')
                ->where('Date', 'between time', $date)
                ->where('p.Status', 1)
                ->where(function ($query) use ($uid) {
                    $query->where('p.User_Id', $uid)
                        ->whereOr('See_User_Id', 'like', '%@,' . $uid . '%');
                })
                ->field('p.ProjectName ,p.EstimatedCost Target, TargetNumber, Project_Id, Sum(Money_Con) Con, Sum(c.Phone+Message) Res')
                ->group('Project_Id')
                ->select();
        }


        foreach ($list as $k => $v) {
            //不显示无消费的数据
            if ($v['Con'] == 0) {
                unset($list[$k]);
                continue;
            }
            // 计算Cos总成本 = Con消费总数/Res线索总数
            if ($v['Res'] != 0) {
                $list[$k]['Cos'] = $v['Con'] / $v['Res'];
            } else {
                $list[$k]['Cos'] = 0.00;
            }
            //计算成本差，实际成本-目标成本
            $list[$k]['Diff'] = $list[$k]['Cos'] - $v['Target'];

            //计算线索数量差,(实际数量/天数)-目标数量
            $list[$k]['ClueDiff'] = $v['Res'] / ($count_date->days + 1) - $list[$k]['TargetNumber'];
        }

        // array(
        //     "ProjectName"=> string "项目名称",
        //     "Target"=>string(3) "目标成本",
        //     "Project_Id"=>int(230),
        //     "Con"=>string(4) "消费总数",
        //     "Res"=> string(1) "线索总数",
        //     "Cos"=>float(昨日成本) ,
        //     "Diff"=> float(成本差)
        //     "ClueDiff"=>float(平均数量 - 目标数量)
        // )
        sort($list); //重建数组索引
        return $list;
    }

    /**
     * 消费图表的数据
     */
    public function get_my_consume()
    {

        $user_id = input('user_id');
        if (!empty($user_id)) {
            $User['p.User_Id'] = $user_id;
        } else {
            $User['p.User_Id'] = ['not null'];
        }

        $Date = [date('Y-m-d', strtotime('-10 day')), date('Y-m-d', strtotime('-1 day'))];

        if (session('type') <= 2) {
            //管理员查看所有
            $list = Db::table('project p')
                ->join('promotion_con c', 'p.Id=c.Project_Id', 'left')
                ->where('Date', 'between time', $Date)
                ->where($User)
                ->where('p.Status', 1)
                ->field('p.Id, p.ProjectName ,Sum(Money_Con) Con,Date')
                ->group('Project_Id,Date')
                ->order('p.Id,Date desc')
                ->select();
        } else {
            $uid = session('id');
            $list = Db::table('project p')
                ->join('promotion_con c', 'p.Id=c.Project_Id', 'left')
                ->where('Date', 'between time', $Date)
                ->where('p.Status', 1)
                ->where(function ($query) use ($uid) {
                    $query->where('p.User_Id', $uid)
                        ->whereOr('See_User_Id', 'like', '%@,' . $uid . '%');
                })
                ->field('p.Id, p.ProjectName, Sum(Money_Con) Con,Date')
                ->group('Project_Id,Date')
                ->order('p.Id,Date desc')
                ->select();
        }

        $result = [
            'labels' => ["前10天", "前9天", "前8天", "前7天", "前6天", "前5天", "前4天", "前3天", "前2天", "昨天"],
            'datasets' => []
        ];

        //获取所有项目名并用项目id作为为索引建值
        $pro = [];
        foreach ($list as $k => $v) {
            $pro[$v['Id']] = [
                'label' => $v['ProjectName'],
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'borderColor' => 'rgba(' . random_int(0, 255) . ',' . random_int(0, 255) . ',' . random_int(0, 255) . ',1)',
                'fill' => false,
                'lineTension' => 0
            ];
        }

        $date_s = [
            date("Y-m-d", strtotime('-10 day')),
            date("Y-m-d", strtotime('-9 day')),
            date("Y-m-d", strtotime('-8 day')),
            date("Y-m-d", strtotime('-7 day')),
            date("Y-m-d", strtotime('-6 day')),
            date("Y-m-d", strtotime('-5 day')),
            date("Y-m-d", strtotime('-4 day')),
            date("Y-m-d", strtotime('-3 day')),
            date("Y-m-d", strtotime('-2 day')),
            date("Y-m-d", strtotime('-1 day')),
        ];
        foreach ($list as $k => $v) {
            $data = $pro[$v['Id']]['data'];
            switch ($v['Date']) {
                case $date_s[0]:
                    $data['0'] = $v['Con'];
                    break;
                case $date_s[1]:
                    $data['1'] = $v['Con'];
                    break;
                case $date_s[2]:
                    $data['2'] = $v['Con'];
                    break;
                case $date_s[3]:
                    $data['3'] = $v['Con'];
                    break;
                case $date_s[4]:
                    $data['4'] = $v['Con'];
                    break;
                case $date_s[5]:
                    $data['5'] = $v['Con'];
                    break;
                case $date_s[6]:
                    $data['6'] = $v['Con'];
                    break;
                case $date_s[7]:
                    $data['7'] = $v['Con'];
                    break;
                case $date_s[8]:
                    $data['8'] = $v['Con'];
                    break;
                case $date_s[9]:
                    $data['9'] = $v['Con'];
                    break;
            }
            $pro[$v['Id']]['data'] = $data;
        }


        foreach ($pro as $k => $v) {
            $result['datasets'][] = $v;
        }

        return json($result);

        // {
        //     labels: ["前7天", "前6天", "前5天", "前4天", "前3天", "前2天", "昨天"],
        //     datasets: [{
        //         label: '项目1',
        //         data: [53, 9, 45, 70, 0, 60, 60],
        //         borderColor: 'rgba(150, 150, 150, 1)',
        //         fill: false,
        //         lineTension: 0
        //     }, {
        //         label: '项目2',
        //         data: [60, 40, 30, 80, 90, 120, 100],
        //         borderColor: 'rgba(100, 240, 150, 1)',
        //         fill: false,
        //         lineTension: 0
        //     }, {
        //         label: '项目3',
        //         data: [51, 12, 36, 80, 20, 10, 12],
        //         borderColor: 'rgba(100, 60, 200, 1)',
        //         fill: false,
        //         lineTension: 0
        //     }]
        // }
    }

    /**
     * 折线图的，消费数据，线索数据，成本数据
     */
    public function get_my_cluedata()
    {
        $where['p.Status'] = 1;
        if (input('project_id')) {
            $where['p.Id'] = input('project_id');
        }
        if (input('user_id')) {
            $where['p.User_Id'] = input('user_id');
        }

        $Date = [date('Y-m-d', strtotime('-30 day')), date('Y-m-d', strtotime('-1 day'))];

        if (session('type') <= 2) {
            //管理员查看所有
            $list = Db::table('project p')
                ->join('promotion_con c', 'p.Id=c.Project_Id', 'left')
                ->where('Date', 'between time', $Date)
                ->where($where)
                ->field('p.Id, p.ProjectName, Sum(Money_Con) Con, Sum(c.Phone+Message) Res, Date')
                ->group('Project_Id,Date')
                ->order('p.Id,Date desc')
                ->select();
        } else {
            $uid = session('id');
            $list = Db::table('project p')
                ->join('promotion_con c', 'p.Id=c.Project_Id', 'left')
                ->where('Date', 'between time', $Date)
                ->where($where)
                ->where(function ($query) use ($uid) {
                    $query->where('p.User_Id', $uid)
                        ->whereOr('See_User_Id', 'like', '%@,' . $uid . '%');
                })
                ->field('p.Id, p.ProjectName, Sum(Money_Con) Con, Sum(c.Phone+Message) Res, Date')
                ->group('Project_Id,Date')
                ->order('p.Id,Date desc')
                ->select();
        }

        $labels = [];
        $datasets = [];

        for ($i = 1; $i <= 30; $i++) {
            $d = 31 - $i;
            $labels[$i - 1] = date('Y-m-d', strtotime('-' . $d . ' day'));
        }

        //获取所有项目名并用项目id作为为索引建值
        foreach ($list as $k => $v) {
            $color = random_int(0, 255) . ',' . random_int(0, 255) . ',' . random_int(0, 255);
            $datasets[$v['Id']] = [
                'label' => $v['ProjectName'],
                'data' => [
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ],
                'backgroundColor' => 'rgba(' . $color . ',0.5)',
                'borderColor' => 'rgba(' . $color . ',1)',
                // 'fill' => false,
                // 'lineTension' => 0//曲率
            ];
        }

        $conData = $datasets; //消费数据
        $clueData = $datasets; //线索数据
        $costData = $datasets; //成本数据


        foreach ($list as $k => $v) {
            $id = $v['Id']; //项目id为键名

            // $conData[$id]['fill'] = false;
            // $conData[$id]['lineTension'] = 0;

            //将对应日期的数据写入
            foreach ($labels as $k2 => $v2) {
                if ($v['Date'] == $v2) {
                    $conData[$id]['data'][$k2] = $v['Con'];
                    $clueData[$id]['data'][$k2] = $v['Res'];

                    if ($v['Res'] == 0) {
                        $costData[$id]['data'][$k2]  = 0;
                    } else {
                        $costData[$id]['data'][$k2]  = round(($v['Con'] / $v['Res']), 2); //消费/线索
                    }
                }
            }
        }

        //重新排序索引
        sort($conData);
        sort($costData);
        sort($clueData);

        return json(['labels' => $labels, 'conData' => $conData, 'costData' => $costData, 'clueData' => $clueData]);
    }

    /**
     * 给客户展示的数据
     */
    public function get_clue_conut()
    {
        $limit = input('limit');
        // $time = date('Y-m-d');
        $start_time = strtotime('2020-05-01 00:00:00');
        $end_time = strtotime('2020-07-17 23:59:59');
        $time = [$start_time, $end_time];

        //计算留言条数
        $customer = Db::table('customer')
            ->where('Client_Id', session('id'))
            ->where('Time', 'between', $time)
            ->field('count(Id) 留言')
            ->select();

        var_dump($customer);

        //推送
        $push = Db::table('push')
            ->where('Client_Id', session('id'))
            ->where('Cre_time', 'between', $time)
            ->field('count(Id) 推送')
            ->select();

        var_dump($push);

        $feiyu = Db::table('feiyu_clues')
            ->where('user_id', session('id'))
            ->where('create_time', 'between', $time)
            ->field('count(Id) 飞鱼线索')
            ->select();

        var_dump($feiyu);
    }
}
