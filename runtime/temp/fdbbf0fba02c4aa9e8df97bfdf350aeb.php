<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:77:"D:\phpstudy_pro\WWW\public/../application/kh_info\view\kh_count\kh_count.html";i:1597728225;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
    <!-- 检索条件 -->
    <form class="layui-form" action="" id="search">
        <div class="layui-container">
            <div class="layui-row">
                <div class="layui-col-xs6 layui-col-sm6 layui-col-md6 layui-col-lg6">
                    <label class="layui-form-label">时间范围</label>
                    <div class="layui-input-inline">
                        <input type="text" name="sel_start_time" id="sel_start_time" placeholder="yyyy-MM-dd HH:mm:ss"
                            autocomplete="off" class="layui-input">
                    </div>
                    -
                    <div class="layui-input-inline">
                        <input type="text" name="sel_end_time" id="sel_end_time" placeholder="yyyy-MM-dd HH:mm:ss"
                            autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-col-xs3 layui-col-sm3 layui-col-md3 layui-col-lg3">
                    <div class="layui-input-inline">
                        <div class="layui-btn layui-btn-normal layui-btn-sm" onclick="sel_time(-1)">前一天</div>
                        <div class="layui-btn layui-btn-normal layui-btn-sm" onclick="sel_time(1)">后一天</div>
                    </div>
                </div>
            </div>
            <div class="layui-row">
                <label class="layui-form-label">分组依据</label>
                <div class="layui-input-inline" style="padding-top: 3px;">
                    <input type="checkbox" name="sel_group[tag]" title="按url标识" checked="">
                    <input type="checkbox" name="sel_group[client]" title="按客户">
                    <input type="checkbox" name="sel_group[project]" title="按项目" checked="">
                    <input type="checkbox" name="sel_group[platform]" title="按平台">
                    <input type="checkbox" name="sel_group[pro_user]" title="按推广账号">
                </div>
                <small>至少选择一个分组依据，不然会出错</small>
            </div>
            <div class="layui-row">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <div class="">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">查找</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- 数据表格 -->
    <table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>

    <script type="text/html" id="head_toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm" lay-event="search">检索</button>
            <button class="layui-btn layui-btn-sm" lay-event="pulls">批量拉取数据</button>
            <button class="layui-btn layui-btn-sm" lay-event="kw">关联</button>
        </div>
    </script>

    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="pull">拉取</button>
        </div>
    </script>

    <script>
        var form = layui.form, table = layui.table, layer = layui.layer, $ = layui.$, laydate = layui.laydate;
        // $.get('/kh_info/kh_info/get_kh_info', function ())
        var form_table = table.render({
            defaultToolbar: ['exports', 'print'],
            elem: '#formfields_table',
            height: "full-70",
            limit: 20,
            limits: [20, 50, 100, 200, 500],
            where: { "sel_group[tag]": "on", "sel_group[project]": "on" },
            page: true,
            // totalRow: true,//开启合计
            url: '/kh_info/kh_count/get_kh_count',
            toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
            method: 'post',
            cols: [[
                { type: 'checkbox' },
                // { field: 'Id', title: 'Id', hide: true },
                { field: 'Client_Nick', title: '客户' },
                { field: 'Pro_Name', title: '项目' },
                { field: 'Platform', title: '平台' },
                { field: 'Pro_User_Id', title: '推广账号Id', hide: true },
                { field: 'Pro_User', title: '推广账号', width: '10%' },
                { field: 'Tag', title: '标识' },
                { field: 'Con_Moblie', title: '留电数量' },
                { field: 'Con_Dialogue', title: '对话数量' },
                { field: 'Con_Visitor', title: '访客数量' },
                { field: 'id', title: 'ID', width: '10%', hide: true }
                , { field: 'impression', title: '展现', }
                , { field: 'click', title: '点击', }
                , { field: 'cost', title: '花费' }
                , { field: 'cpc', title: '平均点击价格' }
                , { field: 'ctr', title: '点击率' }
                , { field: 'cpm', title: '千次展现成本' }
                // , { field: 'name', title: '名称' }
                , { field: 'date', title: '日期', sort: true },
                { field: '', title: '操作', toolbar: '#toolbar', fixed: 'right' }
            ]],
            done: function (res, curr, count) {
                form.render();
            }
        })

        //头工具栏事件
        table.on('toolbar(lay_formfields)', function (obj) {
            var checkStatus = table.checkStatus(obj.config.id);
            switch (obj.event) {
                case 'search':
                    $('#search').slideToggle("slow");
                    // console.log(obj)
                    break;
                case 'pulls':
                    //批量拉取数据
                    var data = checkStatus.data;
                    // 传递给后台
                    var post = '{"data":' + JSON.stringify(data) + '}';
                    $.ajax({
                        url: '/kh_info/api/pulls',
                        data: post,
                        type: 'post',
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function (res) {
                            res = JSON.parse(res);
                            form_table.reload({ data: res, url: '' })
                        }
                    })
                    break;
                case 'kw':
                    layer.open({
                        type: 2,
                        title: "关联",
                        //外部关闭
                        shadeClose: true,
                        area: ['80%', '700px'],
                        offset: '40px',
                        content: '/kh_info/kh_kw/kh_kw'
                    });
                    break;

                // console.log(data)
                // data[0].impression = 5
                // form_table.reload({ data: data, url: '' })
            }
        })

        //监听行工具事件
        table.on('tool(lay_formfields)', function (obj) {
            switch (obj.event) {
                case "pull":
                    // console.log(obj.data);
                    let startDate = $('#sel_start_time').val()
                    let endDate = $('#sel_end_time').val()
                    layer.open({
                        type: 2,
                        title: "编辑",
                        //外部关闭
                        shadeClose: true,
                        area: ['80%', '600px'],
                        offset: '50px',
                        content: '/kh_info/api/get?Pro_User_Id=' + obj.data.Pro_User_Id + '&startDate=' + startDate + '&endDate=' + endDate
                    });
                    break;
            }
        })

        //监听提交
        form.on('submit(formDemo)', function (data) {
            // layer.alert(JSON.stringify(data.field));
            // console.log(data.field);
            data.field['sel_group[tag]'] = data.field['sel_group[tag]'] ? 'on' : '';
            data.field['sel_group[client]'] = data.field['sel_group[client]'] ? 'on' : '';
            data.field['sel_group[project]'] = data.field['sel_group[project]'] ? 'on' : '';
            data.field['sel_group[platform]'] = data.field['sel_group[platform]'] ? 'on' : '';
            data.field['sel_group[pro_user]'] = data.field['sel_group[pro_user]'] ? 'on' : '';
            //重载表格
            form_table.reload({
                where: data.field
            });
            return false;
        });

        //日期渲染
        laydate.render({
            elem: "#sel_start_time",
            type: 'datetime'
        });
        laydate.render({
            elem: "#sel_end_time",
            type: 'datetime'
        });
        form.render()
    </script>


    <script>
        //默认今天
        sel_time(0);

        //监听前一天后一天按钮
        function sel_time(num) {
            //当前时间变量
            var start_time = $('#sel_start_time').val();
            var end_time = $('#sel_end_time').val();
            if (start_time.length < 10) {
                start_time = new Date();
                start_time.setHours(0);
                start_time.setMinutes(0);
                start_time.setSeconds(0);
            }
            if (end_time.length < 10) {
                end_time = new Date();
                end_time.setHours(23);
                end_time.setMinutes(59);
                end_time.setSeconds(59);
            }
            start_time = new Date(start_time);
            end_time = new Date(end_time);
            //新的时间
            var newdate = new Date(start_time.setDate(start_time.getDate() + num));
            var end_time = new Date(end_time.setDate(end_time.getDate() + num));
            var s_str = formatDate(newdate, 'yyyy-MM-dd hh:mm:ss');
            var e_str = formatDate(end_time, 'yyyy-MM-dd hh:mm:ss');
            $('#sel_start_time').val(s_str);
            $('#sel_end_time').val(e_str);
            return false;
        }

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
    </script>

</body>

</html>