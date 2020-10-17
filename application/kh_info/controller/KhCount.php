<?php

namespace app\kh_info\controller;

use app\common\controller\Common;
use think\Db;

class KhCount extends Common
{
    // public function _initialize()
    // {
    //     $this->assign('cli', Cache::get('redis_client'));
    // }

    public function kh_count()
    {
        return view();
    }

    //得到统计数据
    public function get_kh_count()
    {
        $limit = input('limit');
        //查询时间范围,默认查询昨天
        $start_time = input('sel_start_time');
        $end_time = input('sel_end_time');
        if ($start_time) {
            $search['talk_time'] = date('YmdHis', strtotime($start_time));
        } else {
            $search['talk_time'] = date('Ymd') . '000000';
        }
        if ($end_time) {
            $search['end_time'] = date('YmdHis', strtotime($end_time));
        } else {
            $search['end_time'] = date('Ymd') . '235959';
        }

        $where = " where
            kt.talk_time >= " . $search['talk_time'] .
            " and kt.end_time <= " . $search['end_time'];
        // var_dump();

        //分组参数
        $array = input('post.sel_group/a');
        $group = " group by ";

        if (!empty($array['tag'])) {
            $group .= "kk.Tag,";
        }
        if (!empty($array['client'])) {
            $group .= "ui.Name,";
        }
        if (!empty($array['platform'])) {
            $group .= "pu.Platform,";
        }
        if (!empty($array['pro_user'])) {
            $group .= "pu.Pro_User,";
        }
        if (!empty($array['project'])) {
            $group .= "pu.Pro_User,";
        }
        $group = substr($group, 0, strlen($group) - 1);

        $data  = Db::query(
            "
            select
                kk.Id,
                ui.Name as Client_Nick,
                kk.Pro_Name as Pro_Name,
                kk.Tag as Tag,
                pu.Pro_User as Pro_User,
                pu.Id as Pro_User_Id,
                pu.Platform as Platform,
                sum(kt.isMobile) as Con_Moblie,
                sum(kt.isTalk) as Con_Dialogue,
                count(kt.guest_id) as Con_Visitor
            from
                kh_kw kk
                left join user_info ui on kk.Client_Id = ui.User_Id
                left join promotion_user pu on pu.Id = kk.Pro_Id
                inner join (
                    select
                        kt.*
                    from
                        kh_talk kt
                    " . $where . " 
                ) kt on kt.land_page REGEXP kk.Tag
            " . $group . ";"
        );

        $res['code'] = 0;
        $res['count'] = count($data);
        $res['data'] = $data;
        return $res;
    }
}
       // if (!empty($array['tag'])) {
        //     $group .= "b.标识,";
        // }
        // if (!empty($array['client'])) {
        //     $group .= "b.客户,";
        // }
        // if (!empty($array['platform'])) {
        //     $group .= "b.平台,";
        // }
        // if (!empty($array['pro_user'])) {
        //     $group .= "b.推广账号,";
        // }
        // if (!empty($array['project'])) {
        //     $group .= "b.项目,";
        // }
        //GROUP_CONCAT(p.ProjectName) as Pro_Name,
        // $data = Db::query(
        //     "
        //     SELECT
        //         b.客户 as Client_Nick,
        //         b.项目 as Pro_Name,
        //         b.标识 as Tag,
        //         b.Pro_User_Id,
        //         b.推广账号 as Pro_User,
        //         b.平台 as Platform,
        //         sum(a.是否留电) as Con_Moblie,
        //         sum(a.访客是否说话) as Con_Dialogue,
        //         count(a.talk_id) as Con_Visitor
        //     FROM
        //         (
        //             select
        //                 kt.talk_id,
        //                 kt.guest_id,
        //                 kt.isMobile as 是否留电,
        //                 kt.isTalk as 访客是否说话,
        //                 talk_page as 咨询页
        //             from
        //                 kh_talk kt
        //             " . $where . "
        //             group by
        //                 kt.guest_id
        //             ) as a,
        //             (
        //                 select
        //                     kk.Id,
        //                     ui.Name as 客户,
        //                     kk.Pro_Name as 项目,
        //                     kk.Tag as 标识,
        //                     pu.Pro_User as 推广账号,
        //                     pu.Id as Pro_User_Id,
        //                     pu.Platform as 平台
        //                 from
        //                     kh_kw kk
        //                     left join user_info ui on kk.Client_Id = ui.User_Id
        //                     left join promotion_user pu on pu.Id = kk.Pro_Id
        //             ) as b
        //         WHERE
        //             /*INSTR(a.咨询页, b.标识) > 0,*/
        //             a.咨询页 REGEXP b.标识
        //         " . $group . ";"
        // );
