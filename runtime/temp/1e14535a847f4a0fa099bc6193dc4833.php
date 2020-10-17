<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"D:\phpstudy_pro\WWW\public/../application/user\view\rbac\upd_rbac.html";i:1602665836;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="/public/static/jquery.min.js"></script>
    <link rel="stylesheet" href="/public/static/bootstrap3/css/bootstrap.min.css">
</head>

<body>
    <style>
        .a-title {
            width: 100%;
            height: 35px;
            line-height: 35px;
            background-color: rgba(3, 120, 236, 0.3);
            font-size: 24px;
            color: rgb(46, 46, 46);
            text-align: center;
            margin: 20px 0 10px 0;
            border: rgba(204, 214, 224, 0.8) 1px solid;
            border-radius: 5px;
        }

        .check-head {
            background-color: #eee;
            text-align: center;
            width: 100%;
            margin: 20px 0 10px 0;
            border: rgba(204, 214, 224, 0.8) 1px solid;
            border-radius: 5px;
        }
    </style>
    <div class="container">

        <div class="row check-head">
            <div class="col-xs-4 col-sm-3 col-md-1">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="check_all" name="check_all">
                        全选
                    </label>
                </div>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-1">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="check_reverse" name="check_reverse">
                        反选
                    </label>
                </div>
            </div>
        </div>

        <form id='form_auth' action="">
            <input type="text" name="Group_Id" value="<?php echo $Group_Id; ?>" hidden>

            <?php if(is_array($auths) || $auths instanceof \think\Collection || $auths instanceof \think\Paginator): $i = 0; $__LIST__ = $auths;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <div class="a-title">
                <?php switch($name=$key): case "index": ?>首页<?php break; case "website": ?>网站导航模块<?php break; case "project": ?>项目管理<?php break; case "client_rec": ?>客户财务记录模块<?php break; case "promotion": ?>推广账号栏目<?php break; case "client_53": ?>53账号管理模块<?php break; case "kw": ?>客服栏目<?php break; case "customer": ?>留言管理模块<?php break; case "push": ?>推送记录模块<?php break; case "feiyu": ?>飞鱼线索模块<?php break; case "kf_moblie": ?>历史电话模块<?php break; case "kh_info": ?>53访客信息模块<?php break; case "talkdata": ?>话务资料栏目<?php break; case "target": ?>工作目标模块<?php break; case "user": ?>用户管理模块<?php break; case "wxapi": ?>微信用户管理<?php break; default: ?>其他
                <?php endswitch; ?>
            </div>
            <div class="row">
                <?php if(is_array($vo) || $vo instanceof \think\Collection || $vo instanceof \think\Paginator): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
                <div class="col-xs-6 col-sm-4 col-md-3">
                    <div class="checkbox" style="height: 20px;line-height: 20px;margin-top: 0;margin-bottom: 0;">
                        <label>
                            <input type="checkbox" name="rule_ids[<?php echo $list['id']; ?>]" <?php if($list['checked']): ?>checked <?php endif; ?>>
                            <?php echo $list['title']; ?>
                        </label>
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>

            <div style="margin: 30px 0;">
                <button id='submit' type="submit" class="btn btn-primary">立即提交</button>
                <button type="reset" class="btn btn-default">重置</button>
            </div>
        </form>


    </div>

    <script>

        $('#submit').click(function () {
            var r = confirm('确定更改？')
            if (r == true) {
                $.ajax({
                    type: 'post',
                    url: '/public/index.php/upd_rbac_do',
                    data: $('#form_auth').serialize(),
                    dataType: 'json',
                    success: function (res) {
                        alert(res.msg);
                    }
                })
            }
            return false;
        })
        $('#check_all').click(function () {
            var xz = $(this).prop("checked");//判断全选按钮的选中状态
            var ck = $('#form_auth input[type="checkbox"]').prop("checked", xz);  //让其他的选中状态和全选按钮的选中状态一致。 
        })
        $('#check_reverse').click(function () {
            var checkids = $('#form_auth input[type="checkbox"]');
            $.each(checkids, function () {
                //让的选中状态和当前按钮的选中不一致。 
                $(this).prop("checked", !$(this).prop("checked"));
            })
        })
    </script>

</body>

</html>