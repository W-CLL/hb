<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:86:"D:\phpstudy_pro\WWW\public/../application/customer\view\bottommenu\bottommenu_upd.html";i:1602647069;s:20:"./static/header.html";i:1602917348;}*/ ?>
    <head>

    <meta charset="utf-8">

    <title>留言管理</title>

    <meta name="renderer" content="webkit">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <link rel="icon" href="/favicon.ico" type="img/x-ico" />

    <link rel="stylesheet" href="/public/static/layui/css/layui.css">

	<link rel="stylesheet" href="/public/static/css/formfields.css">

     <script type="text/javascript" src="/public/static/layui/layui.all.js" ></script>

     <script type="text/javascript" src="/public/static/layui/lay/modules/laydate.js"></script>

     <script type="text/javascript" src="/public/static/jquery.min.js"></script>

    </head>

<body>
    <form class="layui-form" action="" style="padding: 20px;">

        <input type="text" name="id" hidden value="<?php echo $id; ?>">

        <div class="layui-form-item">
            <label class="layui-form-label">客户</label>
            <div class="layui-input-block">
                <select name="user_id" lay-verify="required">
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-inline">
                <input type="text" name="remark" required lay-verify="required" placeholder="请输入备注"
                    autocomplete="off" value="<?php echo $list['remark']; ?>" class="layui-input">
            </div>
        </div>



        <div class="layui-form-item">
            <label class="layui-form-label">显示</label>
            <div class="layui-input-inline" style="width: 60px;">
                <?php if($list['param']['menu'][0]['show']): ?>
                <input type="checkbox" checked="" name="menu[0][show]" lay-skin="switch">
                <?php else: ?>
                <input type="checkbox" name="menu[0][show]" lay-skin="switch">
                <?php endif; ?>
            </div>
            <label class="layui-form-label">名称</label>
            <div class="layui-input-inline">
                <input type="text" name="menu[0][name]" required lay-verify="required" placeholder="请输入标题"
                    autocomplete="off" value="<?php echo $list['param']['menu'][0]['name']; ?>" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">显示</label>
            <div class="layui-input-inline" style="width: 60px;">
                <?php if($list['param']['menu'][1]['show']): ?>
                <input type="checkbox" checked="" name="menu[1][show]" lay-skin="switch">
                <?php else: ?>
                <input type="checkbox" name="menu[1][show]" lay-skin="switch">
                <?php endif; ?>
            </div>
            <label class="layui-form-label">名称</label>
            <div class="layui-input-inline">
                <input type="text" name="menu[1][name]" required lay-verify="required" placeholder="请输入标题"
                    autocomplete="off" value="<?php echo $list['param']['menu'][1]['name']; ?>" class="layui-input">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label">显示</label>
            <div class="layui-input-inline" style="width: 60px;">
                <?php if($list['param']['menu'][2]['show']): ?>
                <input type="checkbox" checked="" name="menu[2][show]" lay-skin="switch">
                <?php else: ?>
                <input type="checkbox" name="menu[2][show]" lay-skin="switch">
                <?php endif; ?>
            </div>

            <label class="layui-form-label">名称</label>
            <div class="layui-input-inline">
                <input type="text" name="menu[2][name]" required lay-verify="required" placeholder="请输入标题"
                    autocomplete="off" value="<?php echo $list['param']['menu'][2]['name']; ?>" class="layui-input">
            </div>

            <label class="layui-form-label">53链接</label>
            <div class="layui-input-inline">
                <input type="text" name="code53" value="<?php echo $list['param']['code53']; ?>" placeholder="请输入53链接" class="layui-input">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label">显示</label>
            <div class="layui-input-inline" style="width: 60px;">
                <?php if($list['param']['menu'][3]['show']): ?>
                <input type="checkbox" checked="" name="menu[3][show]" lay-skin="switch">
                <?php else: ?>
                <input type="checkbox" name="menu[3][show]" lay-skin="switch">
                <?php endif; ?>
            </div>
            <label class="layui-form-label">名称</label>
            <div class="layui-input-inline">
                <input type="text" name="menu[3][name]" required lay-verify="required" placeholder="请输入标题"
                    autocomplete="off" value="<?php echo $list['param']['menu'][3]['name']; ?>" class="layui-input">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label">显示</label>
            <div class="layui-input-inline" style="width: 60px;">
                <?php if($list['param']['menu'][4]['show']): ?>
                <input type="checkbox" checked="" name="menu[4][show]" lay-skin="switch">
                <?php else: ?>
                <input type="checkbox" name="menu[4][show]" lay-skin="switch">
                <?php endif; ?>
            </div>
            <label class="layui-form-label">名称</label>
            <div class="layui-input-inline">
                <input type="text" name="menu[4][name]" required lay-verify="required" placeholder="请输入标题"
                    autocomplete="off" value="<?php echo $list['param']['menu'][4]['name']; ?>" class="layui-input">
            </div>

            <label class="layui-form-label">电话号</label>
            <div class="layui-input-inline">
                <input type="text" name="phone" required placeholder="请输入电话号码" autocomplete="off"
                    value="<?php echo $list['param']['phone']; ?>" class="layui-input">
            </div>
        </div>






        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo">立即修改</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>

    <script>
        //Demo
        layui.use('form', function () {
            var form = layui.form, $ = layui.$;

            //监听提交
            form.on('submit(formDemo)', function (data) {
                // console.log(JSON.stringify(data.field))
                $.post('/public/index.php/bottommenu_upd', data.field, function (res) {
                    // console.log(res)
                    if (res.code == 0) {
                        layer.alert(res.msg, function () {
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layui.table.reload("formfields_table");//刷新父页面的表格
                            parent.layer.close(index); //再执行关闭
                        });
                    } else {
                        layer.alert(res.msg)
                    }
                })
                return false;
            });

            form.render();
        });
    </script>

</body>