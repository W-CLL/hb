<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:74:"D:\phpstudy_pro\WWW\public/../application/push\view\kf_moblie\history.html";i:1597728212;s:20:"./static/header.html";i:1602579388;s:27:"./static/js/selectdate.html";i:1597727918;}*/ ?>
    <head>

    <meta charset="utf-8">

    <title>留言管理</title>

    <meta name="renderer" content="webkit">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <link rel="icon" href="/favicon.ico" type="img/x-ico" />

    <link rel="stylesheet" href="/public/static/layui/css/layui.css">

	<link rel="stylesheet" href="/public/static/css/formfields.css">

     <script src="/public/static/layui/layui.all.js" ></script>

     <script src="/public/static/jquery.min.js"></script>

    </head>

<body>

    <div class="layui-container">
        <div class="layui-row">

            <div class="layui-inline">
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="sel_time" placeholder="请选择日期范围">
                </div>
                <button class="layui-btn layui-btn-normal layui-btn-sm" id="lastday">前一天</button>
                <div class="layui-input-inline">
                    <button class="layui-btn layui-btn-normal layui-btn-sm" id="nextday">后一天</button>
                </div>
            </div>

            <div class="layui-inline">
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="sel_phone" placeholder="请输入手机号码">
                </div>
            </div>

            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn layui-btn-sm laymy-w1" id="search">搜索</button>
                </div>
            </div>

            <div class="layui-inline" style="margin-left: 200px;">
                <div class="layui-input-inline">
                    <a href="/push/kf_moblie/kfmoblie" class="layui-btn layui-btn-sm">53推送历史电话</a>
                </div>
            </div>

        </div>
    </div>

    <table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>

    <!-- 导入日期选择扩展 -->
    <!-- 这是时间范围选择功能的内容，注意这是扩展后的laydate.js与原版不同 -->
<script src="/static/layui/lay/modules/laydate.js"></script>
<script>
    var laydate = layui.laydate;
    // 定义接收本月的第一天和最后一天
    var startDate1 = new Date(new Date().setDate(1));
    var endDate1 = new Date(new Date(new Date().setMonth(new Date().getMonth() + 1)).setDate(0));
    // 定义接收上个月的第一天和最后一天
    var startDate2 = new Date(new Date(new Date().setMonth(new Date().getMonth() - 1)).setDate(1));
    var endDate2 = new Date(new Date().setDate(0));
    //日期范围
    laydate.render({
        elem: '#sel_time'
        , range: true,
        extrabtns: [
            { id: 'today', text: '今天', range: [new Date(), new Date()] },
            {
                id: 'yesterday', text: '昨天', range: [new Date(new Date().setDate(new Date().getDate() - 1)),
                new Date(new Date().setDate(new Date().getDate() - 1))]
            },
            { id: 'lastday-7', text: '过去7天', range: [new Date(new Date().setDate(new Date().getDate() - 7)), new Date(new Date().setDate(new Date().getDate() - 1))] },
            { id: 'lastday-30', text: '过去30天', range: [new Date(new Date().setDate(new Date().getDate() - 30)), new Date(new Date().setDate(new Date().getDate() - 1))] },

            { id: 'thismonth', text: '本月', range: [startDate1, endDate1] },
            { id: 'lastmonth', text: '上个月', range: [startDate2, endDate2] }
        ]
    });
    //格式化日期函数
    function formatDate(date, fmt) {
        if (typeof date == 'string') {
            return date;
        }

        if (!fmt) fmt = "yyyy-MM-dd hh:mm:ss";

        if (!date || date == null) return null;
        var o = {
            'M+': date.getMonth() + 1, // 月份
            'd+': date.getDate(), // 日
            'h+': date.getHours(), // 小时
            'm+': date.getMinutes(), // 分
            's+': date.getSeconds(), // 秒
            'q+': Math.floor((date.getMonth() + 3) / 3), // 季度
            'S': date.getMilliseconds() // 毫秒
        }
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length))
        for (var k in o) {
            if (new RegExp('(' + k + ')').test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (('00' + o[k]).substr(('' + o[k]).length)))
        }
        return fmt
    }

    //监听前一天，后一天按钮单击
    $(document).ready(function () {
        $('#lastday').click(function () {
            //当前时间变量
            var nowdate = $('#sel_time').val();
            if (nowdate.length < 10) {
                nowdate = new Date();
            } else {
                nowdate = new Date(nowdate.substr(0, 10));
            }
            //新的时间
            var newdate = new Date(nowdate.setDate(nowdate.getDate() - 1));
            var str = formatDate(newdate, 'yyyy-MM-dd');
            $('#sel_time').val(str + ' - ' + str);
            return false;
        });
        $('#nextday').click(function () {
            var nowdate = $('#sel_time').val();
            if (nowdate.length < 10) {
                nowdate = new Date();
            } else {
                nowdate = new Date(nowdate.substr(13, 10));
            }
            var newdate = new Date(nowdate.setDate(nowdate.getDate() + 1));
            var str = formatDate(newdate, 'yyyy-MM-dd');
            $('#sel_time').val(str + ' - ' + str);
            return false;
        })

    });
</script>
    <script>
        var table = layui.table, layer = layui.layer, $ = layui.$, laypage = layui.laypage, laydate = layui.laydate;

        //日期范围
        laydate.render({
            elem: '#sel_time'
            , range: true,
        });


        var form_table = table.render({
            elem: '#formfields_table',
            height: "full-70",
            limit: 20,
            limits: [20, 50, 100, 200, 500],
            page: true,
            totalRow: true,//开启合计
            toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
            defaultToolbar: ['exports', 'print'],
            url: '/push/kf_moblie/getHistory',
            method: 'get',
            cols: [[
                { field: 'Id', title: 'ID', width: 80 },
                { field: "Client_Id", title: '客户Id', width: 70 },
                { field: "Name", title: '客户', width: 200 },
                { field: 'Content', title: '号码或内容' },
                { field: 'Cre_time', title: '时间', width: 200 },
                { field: 'ColumnType', title: '模块', width: 200 },
            ]],
        });

        //监听列工具行
        table.on('tool(lay_formfields)', function (obj) {
            console.log(obj.data)
            var data = obj.data;
            var event = obj.event;
            switch (event) {

            }
        });

        //监听单元格编辑
        table.on('edit(lay_formfields)', function (obj) {
            // console.log(obj.data)
            var value = obj.value //得到修改后的值
                , data = obj.data //得到所在行所有键值
                , field = obj.field; //得到字段
        });

        //搜索
        $("#search").click(function () {

            var sel_client_id = $("#sel_client_id").val() || "";
            var sel_time = $('#sel_time').val() || "";
            var sel_phone = $('#sel_phone').val() || "";

            form_table.reload({	//重载表格
                where: {
                    sel_client_id: sel_client_id,
                    sel_time: sel_time,
                    sel_phone: sel_phone,
                }
            });
        });
    </script>

</body>