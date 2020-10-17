<?php

namespace app\feiyu\model;

use Exception;
use think\Model;
use think\Db;

class Feiyu extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'feiyu_clues';
    //获取线索数据
    public function get_clues($where, $limit)
    {
        $data = $this
            ->where($where)
            ->alias('fc')
            ->field('fc.*,fu.Client_Id,fu.Name')
            ->join('feiyu_user fu', 'fu.Id=fc.client_id')
            ->group('fc.create_time desc,fc.Id desc')
            ->paginate($limit);

        $data = $data->toArray($data);

        $result = $data['data'];
        $count = $data['total'];

        //对返回数据做一些处理
        foreach ($result as $k => $v) {
            $result[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $result[$k]['ok_time'] = $result[$k]['ok_time'] ? date('Y-m-d H:i:s', $v['ok_time']) : '';
            switch ($v['clue_type']) {
                case '0':
                    $result[$k]['clue_type'] = '表单提交';
                    break;
                case '1':
                    $result[$k]['clue_type'] = '在线咨询';
                    break;
                case '2':
                    $result[$k]['clue_type'] = '智能电话';
                    break;
                case '3':
                    $result[$k]['clue_type'] = '网页回呼';
                    break;
                case '4':
                    $result[$k]['clue_type'] = '卡券';
                    break;
                case '5':
                    $result[$k]['clue_type'] = '抽奖';
                    break;
                default:
                    $result[$k]['clue_type'] = '其他';
            }
            //  0:外部流量,1:正常投放,2:外部导入,3:异常提交,4:广告预览,5:抖音私信,6:鲁班线索
            switch ($v['clue_source']) {
                case '0':
                    $result[$k]['clue_source'] = '外部流量';
                    break;
                case '1':
                    $result[$k]['clue_source'] = '正常投放';
                    break;
                case '2':
                    $result[$k]['clue_source'] = '外部导入';
                    break;
                case '3':
                    $result[$k]['clue_source'] = '异常提交';
                    break;
                case '4':
                    $result[$k]['clue_source'] = '广告预览';
                    break;
                case '5':
                    $result[$k]['clue_source'] = '抖音私信';
                    break;
                case '6':
                    $result[$k]['clue_source'] = '鲁班线索';
                    break;
                default:
                    $result[$k]['clue_source'] = '其他';
                    break;
            }
            // switch ($v['ok_status']) {
            //     case '0':
            //         $result[$k]['ok_status'] = '待提取';
            //         break;
            //     case '1':
            //         $result[$k]['ok_status'] = '已提取';
            //         break;
            // }

            switch ($v['clue_convert_state']) {
                case '0':
                    $result[$k]['clue_convert_state'] = '待回传';
                    break;
                case '1':
                    $result[$k]['clue_convert_state'] = '无效线索';
                    break;
                case '2':
                    $result[$k]['clue_convert_state'] = '潜在客户';
                    break;
                case '3':
                    $result[$k]['clue_convert_state'] = '高价值客户';
                    break;
                case '4':
                    $result[$k]['clue_convert_state'] = '已成单';
                    break;
            }
            // 广告主名称、广告计划名称、组件名字/ID：客户不可见、竞价可见
            if (session('type') == 5) {
                $result[$k]['adv_name'] = '';
                $result[$k]['ad_plan_name'] = '';
                $result[$k]['module_name'] = '';
                $result[$k]['module_id'] = '';
            }
            if (session('type') > 2 && session('type') != 5) {
                $result[$k]['telphone'] = substr_replace($v['telphone'], '****', '3', '4');
            }
        }

        //尝试消除不展示的字段
        try {
            $set_hide = json_decode(file_get_contents('./static/json/setFeiyuHideField.json'), true);
            if (isset($set_hide[session('id')])) {
                $array_hide = $set_hide[session('id')]['hide'];
                foreach ($array_hide as $k => $v) {
                    foreach ($result as $k2 => $v2) {
                        // unset($result[$k2][$v]);
                        $result[$k2][$v] = '-';
                    }
                }
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
        
        // var_dump($result);
        return ['count' => $count, 'data' => $result];
    }
}
