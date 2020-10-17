<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:84:"D:\phpstudy_pro\WWW\public/../application/client_rec\view\client_rec\client_sum.html";i:1597728233;s:20:"./static/header.html";i:1602917348;}*/ ?>
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
<div class="layui-container">
    <div class="layui-row">
        <?php if(\think\Session::get('auth')<3): ?>
        <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <select id="sel_client_id"   class="layui-select">
                        <option value="">请选择客户</option>
                        <?php foreach($cli as $k=>$v): ?>
                        <option value="<?php echo $v['User_Id']; ?>"><?php echo $v['Name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn layui-btn-sm laymy-w1" id="search">搜索</button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>
<script>
    var table = layui.table,layer = layui.layer,$=layui.$,laydate = layui.laydate,laypage=layui.laypage
    var form_table = table.render({
        elem:'#formfields_table',
        height:"full-70",
        totalRow: true,
        toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
        defaultToolbar:['exports','print'],
        url:'/get_client_sum',
        method:'get',
        cols:[[
            {field:'Client_Id',title:'ID',hide:true},
            <?php if(\think\Session::get('auth')<3): ?>	{field:'Name',title:'客户',totalRowText: '合计',sort:true},<?php endif; ?>
            {field:'Money',title:'总充值金额',sort:true,totalRow: true,},
            {field:'Cli_Money_Con',title:'总消费金额',sort:true,totalRow: true,},
            {field:'Sum',title:'总余额',sort:true,totalRow: true,},
        ]],
        done:function(res, curr, count){

        }
    });

    //将上述表格示例导出为 csv 文件


    //搜索
    $("#search").click(function(){
        var sel_client_id = $("#sel_client_id").val()||"";
        form_table.reload({	//重载表格
            where:{
                sel_client_id:sel_client_id,
            }
        });
    });

</script>
</body>