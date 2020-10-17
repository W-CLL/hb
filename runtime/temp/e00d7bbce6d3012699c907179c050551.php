<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:82:"D:\phpstudy_pro\WWW\public/../application/customer\view\bottommenu\bottommenu.html";i:1602655975;s:20:"./static/header.html";i:1602917348;}*/ ?>
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
    <table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>
    <script type="text/html" id="head_toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="add">新增</button>
    </div>
    </script>
    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="show">查看</button>
            <button class="layui-btn layui-btn-sm" lay-event="upd">修改</button>
            <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="delete">删除</button>
        </div>
    </script>

    <script>

        var table = layui.table, layer = layui.layer, $ = layui.$, laydate = layui.laydate;
        var form_table = table.render({
            elem: '#formfields_table',
            height: "full-70",
            toolbar: '#head_toolbar',
            defaultToolbar: '',
            url: '/public/index.php/bottommenu',
            method: 'post',
            cols: [[
                { field: 'uniqid', title: 'ID', width: 150 },
                //{ field: 'id', title: 'ID', width: 50 },
                { field: 'name', title: '用户', width: 200 },
                { field: 'JSpath', title: 'js路径' },
                { field: 'remark', title: '备注'},
                { field: '', title: '操作', toolbar: '#toolbar', width: 200 }
            ]],
            done: function (res, curr, count) {
            }

        });
        //监听头部工具行
        table.on('toolbar(lay_formfields)', function (obj) {
            var data = obj.data;
            var event = obj.event;
            switch (event) {
                case 'add':
                    {
                        layer.open({
                            type: 2,
                            title: "添加底部菜单",
                            shadeClose: true,//外部关闭
                            area: ['50%', '600px'],
                            content: '/public/index.php/bottommenu_add'
                        });

                        break;
                    }
            }
        });

        //监听列工具行
        table.on('tool(lay_formfields)', function (obj) {
            var data = obj.data;
            var event = obj.event;
            var tr = obj.tr;
            switch (event) {
                case 'delete':
                    {
                        layer.confirm('确定删除？（注意：删除将不可恢复）', { shadeClose: true ,icon:3}, function () {
                            $.ajax({
                                type: 'delete',
                                url: '/public/index.php/bottommenu_del',
                                data: data,
                                success: function (res) {
                                    layer.alert(res.msg, function (index) {
                                        if (res.code == 0) {
                                            layui.table.reload("formfields_table");//刷新页面的表格
                                        }
                                        layer.close(index); //再执行关闭
                                    });
                                }
                            })
                        })
                        break;
                    }
                case 'show':
                    {
                        window.open('/public/index.php/bottommenu_show/show/uniqid/' + data.uniqid);
                        break;
                    }
                case 'upd':
                    {
                        layer.open({
                            type: 2,
                            title: "修改底部菜单",
                            shadeClose: true,//外部关闭
                            area: ['50%', '600px'],
                            content: '/public/index.php/bottommenu_upd/?id='+ data.uniqid
                        });

                        break;
                    }
            }
        });

    </script>
</body>