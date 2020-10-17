<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
//api接口
//Route::rule('api_massage/key/:key','api/api/message','post');

//socket推送
Route::resource('push', 'push/push', ['var' => ['push' => 'Id']]);
Route::post('update_push_remark', 'push/Push/update_push_remark');
return [
    '__pattern__' => [
        'name' => '\w+'
    ],
    '[hello]' => [
        ':id' => [
            'index/hello',
            [
                'method' => 'get'
            ],
            [
                'id' => '\d+'
            ]
        ],
        ':name' => [
            'index/hello',
            [
                'method' => 'post'
            ]
        ]
    ],

    '__alias__' => [
        'login' => 'login/login/login',
        'checklogin' => 'login/login/checklogin',
        'loginout' => 'login/login/loginout',
        //首页index
        'index' => 'index/index/index',
        'clear_redis' => 'index/index/clear_redis',
        'main' => 'index/main/main',
        'get_my_project' => 'index/main/get_my_project', //项目数据图表
        'get_my_consume' => 'index/main/get_my_consume', //消费数据图表
        'get_clue_conut' => 'index/main/get_clue_conut',
        'get_my_cluedata' => 'index/main/get_my_cluedata',

        //用户账号user
        'user' => 'user/user/user',
        'get_user' => 'user/user/get_user',
        'ins_user' => 'user/user/ins_user',
        'ins_user_do' => 'user/user/ins_user_do',
        'del_user_do' => 'user/user/del_user_do',
        'dels_user_do' => 'user/user/dels_user_do',
        'reset_user' => 'user/user/reset_user',
        'reset_user_do' => 'user/user/reset_user_do',
        'upd_user' => 'user/user/upd_user',
        'upd_user_do' => 'user/user/upd_user_do',
        'upd_msg' => 'user/user/upd_msg',
        'upd_msg_do' => 'user/user/upd_msg_do',

        //权限管理
        'rbac' => 'user/rbac/rbac',
        'get_rbac' => 'user/rbac/get_rbac',
        'ins_rbac' => 'user/rbac/ins_rbac',
        'ins_rbac_do' => 'user/rbac/ins_rbac_do',

        'upd_rbac' => 'user/rbac/upd_rbac',
        'get_upd_rbac' => 'user/rbac/get_upd_rbac',
        'upd_rbac_do' => 'user/rbac/upd_rbac_do',

        'auth_list' => 'user/rbac/auth_list', #权限列表
        'upd_auth' => 'user/rbac/upd_auth', #更新权限规则
        'del_auth' => 'user/rbac/del_auth', #删除权限规则

        //公告管理
        'notice' => 'notice/notice/notice',
        'add_notice' => 'notice/notice/add_notice',     //添加通告
        'edit_notice' => 'notice/notice/edit_notice',   //修改通告
        'del_notice' => 'notice/notice/del_notice',     //删除通告
        'notice_list' => 'notice/notice/notice_list',   //以往公告列表


        //角色管理
        'add_role' => 'user/role/add_role',
        'del_role' => 'user/role/del_role',
        'leftmenu' => 'user/role/leftmenu', //控制对应角色左侧菜单显示

        //客户53账号
        'client_53' => 'client_53/client_53/client_53',
        'get_client_53' => 'client_53/client_53/get_client_53',
        'ins_client_53' => 'client_53/client_53/ins_client_53',
        'ins_client_53_do' => 'client_53/client_53/ins_client_53_do',
        'dels_client_53_do' => 'client_53/client_53/dels_client_53_do',
        'upd_client_53' => 'client_53/client_53/upd_client_53',
        'upd_client_53_do' => 'client_53/client_53/upd_client_53_do',
        'get_kf53_key' => 'client_53/client_53/get_kf53_key',

        //推广账号promotion
        'promotion' => 'promotion/promotion/promotion',
        'get_promotion' => 'promotion/promotion/get_promotion',
        'ins_promotion' => 'promotion/promotion/ins_promotion',
        'ins_promotion_do' => 'promotion/promotion/ins_promotion_do',
        'del_promotion_do' => 'promotion/promotion/del_promotion_do',
        'dels_promotion_do' => 'promotion/promotion/dels_promotion_do',
        'reg_promotion_do' => 'promotion/promotion/reg_promotion_do',
        'upd_promotion' => 'promotion/promotion/upd_promotion',
        'upd_promotion_do' => 'promotion/promotion/upd_promotion_do',
        'get_pro_key' => 'promotion/promotion/get_pro_key',

        //推广账号充值promotionRec
        'promotion_rec' => 'promotion/promotionRec/promotion_rec',
        'get_promotion_rec' => 'promotion/promotionRec/get_promotion_rec',
        'ins_promotion_rec' => 'promotion/promotionRec/ins_promotion_rec',
        'ins_promotion_rec_do' => 'promotion/promotionRec/ins_promotion_rec_do',
        'dels_promotion_rec_do' => 'promotion/promotionRec/dels_promotion_rec_do',
        'upd_promotion_rec' => 'promotion/promotionRec/upd_promotion_rec',
        'upd_promotion_rec_do' => 'promotion/promotionRec/upd_promotion_rec_do',
        //已打款和已到账按钮
        'havepay_promotion_rec_do' => 'promotion/promotionRec/havepay_promotion_rec_do',
        'havebill_promotion_rec_do' => 'promotion/promotionRec/havebill_promotion_rec_do',
        'detail_promotion_rec' => 'promotion/promotionRec/detail_promotion_rec',

        //客户打款记录
        'client_rec' => 'client_rec/ClientRec/client_rec',
        'get_client_rec' => 'client_rec/ClientRec/get_client_rec',
        'ins_client_rec' => 'client_rec/ClientRec/ins_client_rec',
        'ins_client_rec_do' => 'client_rec/ClientRec/ins_client_rec_do',
        'dels_client_rec_do' => 'client_rec/ClientRec/dels_client_rec_do',
        'upd_client_rec' => 'client_rec/ClientRec/upd_client_rec',
        'upd_client_rec_do' => 'client_rec/ClientRec/upd_client_rec_do',

        //客户余额统计
        'client_sum' => 'client_rec/ClientRec/client_sum',
        'get_client_sum' => 'client_rec/ClientRec/get_client_sum',
        //消费合计
        'client_count' => 'client_rec/ClientCount/client_count',
        'get_client_count' => 'client_rec/ClientCount/get_client_count',

        //竞价首页项目管理project
        'project' => 'project/project/project',
        'get_project' => 'project/project/get_project',
        'view_project' => 'project/project/view_project',
        'get_view_project' => 'project/project/get_view_project',
        'ins_project' => 'project/project/ins_project',
        'ins_project_do' => 'project/project/ins_project_do',
        'del_project_do' => 'project/project/del_project_do',
        'dels_project_do' => 'project/project/dels_project_do',
        'reg_project_do' => 'project/project/reg_project_do',
        'upd_project' => 'project/project/upd_project',
        'upd_project_do' => 'project/project/upd_project_do',
        'upd_project_changes' => 'project/project/upd_project_changes',
        'log_project' => 'project/project/log_project',
        'log_project_add' => 'project/project/log_project_add',
        'ok_status' => 'project/project/ok_status',

        //竞价消费录入promotionCon
        'promotion_con' => 'promotion/promotionCon/promotion_con',
        'get_promotion_con' => 'promotion/promotionCon/get_promotion_con',
        'ins_promotion_con' => 'promotion/promotionCon/ins_promotion_con',
        'get_ins_promotion_con' => 'promotion/promotionCon/get_ins_promotion_con',
        'ins_promotion_con_do' => 'promotion/promotionCon/ins_promotion_con_do',
        'dels_promotion_con_do' => 'promotion/promotionCon/dels_promotion_con_do',
        'upd_promotion_con' => 'promotion/promotionCon/upd_promotion_con',
        'upd_promotion_con_do' => 'promotion/promotionCon/upd_promotion_con_do',
        'upd_promotion_con_all_do' => 'promotion/promotionCon/upd_promotion_con_all_do',
        //外部消费
        'cli_promotion_con' => 'promotion/promotionCon/cli_promotion_con',
        'get_cli_promotion_con' => 'promotion/promotionCon/get_cli_promotion_con',
        'upd_cli_promotion_con' => 'promotion/promotionCon/upd_cli_promotion_con',
        'upd_cli_promotion_con_do' => 'promotion/promotionCon/upd_cli_promotion_con_do',


        //客服栏目
        'kw' => 'kw/kw/kw',
        'get_kw' => 'kw/kw/get_kw',
        'input' => 'kw/kw/input',
        'urlinput' => 'kw/kw/urlinput',
        'ins_kw' => 'kw/kw/ins_kw',
        'ins_kw_do' => 'kw/kw/ins_kw_do',
        'dels_kw' => 'kw/kw/dels_kw',
        'upd_kw' => 'kw/kw/upd_kw',
        'upd_kw_do' => 'kw/kw/upd_kw_do',

        //留言板栏目
        'customer' => 'customer/customer/customer',
        'get_customer' => 'customer/customer/get_customer',
        'dels_customer_do' => 'customer/customer/dels_customer_do',
        'ok_customer_do' => 'customer/customer/ok_customer_do',
        'customer_count' => 'customer/customer/customer_count',
        'get_customer_count' => 'customer/customer/get_customer_count',
        'upd_customer_remark' => 'customer/customer/upd_customer_remark',
        'ApiKfMoblie' => 'api/kf/kf_moblie',
        'getKw' => 'api/api/getKw',

        //目标管理
        'target' => 'target/target/target',
        'get_target' => 'target/target/get_target',
        'ins_target' => 'target/target/ins_target',
        'ins_target_do' => 'target/target/ins_target_do',
        'upd_target' => 'target/target/upd_target',
        'upd_target_do' => 'target/target/upd_target_do',
        'dels_target_do' => 'target/target/dels_target_do',
        'finsh_target' => 'target/target/finsh_target',
        // 工作餐
        'meal' => 'target/meal/meal',
        'get_meal' => 'target/meal/get_meal',
        'ins_meal' => 'target/meal/ins_meal',
        'ins_meal_do' => 'target/meal/ins_meal_do',
        'upd_meal' => 'target/meal/upd_meal',
        'upd_meal_do' => 'target/meal/upd_meal_do',
        'del_meal_do' => 'target/meal/del_meal_do',

        //竞品资料
        'com_data' => 'talkdata/data/com_data', //竞品资料主页
        'show_com_data' => 'talkdata/data/show_com_data', //竞品资料详情页
        'manage_com_data' => 'talkdata/data/manage_com_data', //竞品资料管理页
        'get_manage_com_data' => 'talkdata/data/get_manage_com_data',
        'add_com_data_cate' => 'talkdata/data/add_com_data_cate', //添加竞品资料分类
        'upd_com_data_cate' => 'talkdata/data/upd_com_data_cate', //修改竞品资料分类
        'del_com_data_cate' => 'talkdata/data/del_com_data_cate', //删除竞品资料分类
        'add_com_data' => 'talkdata/data/add_com_data', //添加竞品资料
        'upd_com_data' => 'talkdata/data/upd_com_data', //修改竞品资料
        'del_com_data' => 'talkdata/data/del_com_data', //删除竞品资料
        'imgupload' => 'talkdata/data/imgupload', //图片上传
        'save_liuyan' => 'talkdata/data/save_liuyan', //保存留言
        'upd_liuyan' => 'talkdata/data/upd_liuyan', //更新状态
        'jump_site' => 'talkdata/data/jump_site', //跳转地址

        //招商话术
        'attr_talk' => 'talkdata/talk/index', //招商话术主页
        'add_talk_cate' => 'talkdata/talk/add_talk_cate', //添加招商话术分类
        'edit_talk_cate' => 'talkdata/talk/edit_talk_cate', //编辑招商话术分类
        'del_talk_cate' => 'talkdata/talk/del_talk_cate', //删除招商话术分类
        'edit_talks' => 'talkdata/talk/edit_talks', //编辑话术
        'get_edit_talk' => 'talkdata/talk/get_edit_talk', //得到编辑话术内容数据
        'get_talks' => 'talkdata/talk/get_talks', //得到单个分类的全部对话数据
        'upd_talk' => 'talkdata/talk/upd_talk', //更新单条对话
        'del_talk' => 'talkdata/talk/del_talk', //删除单挑对话
        'save_talks' => 'talkdata/talk/save_talks', //保存对话
        // 注册
        'register' => 'login/register/register', //注册
        //微信公众号
        'wxgzh' => 'wxapi/wxgzh/index', //提供给微信公众号的接口
        'bindwx' => 'wxapi/wx/bindwx', //绑定微信
        'wx_qrcode' => 'wxapi/wx/qrcode', //生成二维码
        'wx_login' => 'wxapi/wx/wx_login', //使用微信登录的接口
        'wx_login_do' => 'wxapi/wx/wx_login_do',

        //落地页底部菜单制作
        'bottommenu' => 'customer/bottommenu/bottommenu',
        'bottommenu_add' => 'customer/bottommenu/bottommenu_add',
        'bottommenu_show' => 'customer/bottommenu/bottommenu_show',
        'bottommenu_del' => 'customer/bottommenu/bottommenu_del',
        'bottommenu_upd' => 'customer/bottommenu/bottommenu_upd',

    ]
];
