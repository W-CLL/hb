<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"D:\phpstudy_pro\WWW\public/../application/user\view\rbac\rbac.html";i:1602665815;s:20:"./static/header.html";i:1602917348;}*/ ?>
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
        <button class="layui-btn layui-btn-sm" lay-event="insert">新增权限类型</button>
        <button class="layui-btn layui-btn-sm" lay-event="auth_list">权限列表</button>
        <button class="layui-btn layui-btn-sm" lay-event="add_role">新增角色</button>
    </div>
    </script>
    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm" lay-event="update">编辑权限</button>
            <button class="layui-btn layui-btn-sm" lay-event="leftmenu">左侧菜单</button>
            <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="delete">删除角色</button>
        </div>
    </script>

    <script>

        var table = layui.table, layer = layui.layer, $ = layui.$, laydate = layui.laydate;
        var form_table = table.render({
            elem: '#formfields_table',
            height: "full-70",
            toolbar: '#head_toolbar',
            defaultToolbar: '',
            url: '/public/index.php/get_rbac',
            method: 'get',
            cols: [[
                { field: 'Type_Id', title: 'ID', width: 50 },
                { field: 'Type_Name', title: '角色名称' },
                { field: 'Group_Id', title: '外键组Id' },
                { field: '', title: '操作', toolbar: '#toolbar' }
            ]],
            done: function (res, curr, count) {
            }

        });
        //监听头部工具行
        table.on('toolbar(lay_formfields)', function (obj) {
            var data = obj.data;
            var event = obj.event;
            switch (event) {
                case 'insert':
                    {
                        layer.open({
                            type: 2,
                            title: "新增权限",
                            shadeClose: true,//外部关闭
                            area: ['50%', '600px'],
                            content: '/public/index.php/ins_rbac'
                        });

                        break;
                    }
                case 'auth_list':
                    {
                        location.href = '/public/index.php/auth_list'
                        break;
                    }
                case 'add_role':
                    {
                        layer.open({
                            type: 2,
                            title: '新增角色',
                            shadeClose: true,//外部关闭
                            area: ['50%', '600px'],
                            content: '/public/index.php/add_role'
                        })
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
                case 'update':
                    {
                        layer.open({
                            type: 2,
                            title: "编辑角色权限",
                            shadeClose: true,//外部关闭
                            area: ['80%', 600],
                            content: '/public/index.php/upd_rbac?Group_Id=' + obj.data.Group_Id
                        });
                        break;
                    }
                case 'delete':
                    {
                        layer.confirm('确定删除？',{ shadeClose: true }, function () {
                            $.ajax({
                                type: 'delete',
                                url: '/public/index.php/del_role',
                                data: data,
                                success: function (res) {
                                    layer.alert(res.msg, function (index) {
                                        if (res.code === 0) {
                                            layui.table.reload("formfields_table");//刷新页面的表格
                                        }
                                        layer.close(index); //再执行关闭
                                    });
                                }
                            })
                        })
                        break;
                    }
                case 'leftmenu':
                    {
                        layer.open({
                            type: 2,
                            title: "编辑角色菜单",
                            shadeClose: true,//外部关闭
                            area: ['80%', 600],
                            content: '/public/index.php/leftmenu/type/group_id/' + obj.data.Group_Id
                        });
                        break;
                    }
            }
        });

    </script>
</body>