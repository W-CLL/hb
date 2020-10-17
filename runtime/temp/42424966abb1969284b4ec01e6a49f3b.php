<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:75:"D:\phpstudy_pro\WWW\public/../application/kh_info\view\kh_info\kh_info.html";i:1597728224;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
    <form class="layui-form layui-form-item" action="" id="search" hidden>
        <br>
        <!-- <div class="layui-form-item">
            <label class="layui-form-label">选择框项目</label>
            <div class="layui-input-inline">
                <select name="city">
                    <option value=""></option>
                    <option value="0">项目列表</option>
                </select>
            </div>
        </div> -->
        <div class="layui-form-item">
            <label class="layui-form-label">访客ID</label>
            <div class="layui-input-inline">
                <input type="text" name="sel_guest_id" placeholder="访客ID" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">客服工号</label>
            <div class="layui-input-inline">
                <input type="text" name="sel_worker_id" placeholder="客服工号" autocomplete="off" class="layui-input">
            </div>
            <!-- <div class="layui-form-mid layui-word-aux">辅助文字</div> -->
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">着陆页</label>
            <div class="layui-input-inline">
                <input type="text" name="sel_land_page" placeholder="着陆页面关键词" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">着陆页面关键词</div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">时间范围</label>
            <div class="layui-input-inline">
                <input type="text" name="sel_start_time" id="sel_start_time" placeholder="yyyy-MM-dd HH:mm:ss"
                    autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline">
                <input type="text" name="sel_end_time" id="sel_end_time" placeholder="yyyy-MM-dd HH:mm:ss"
                    autocomplete="off" class="layui-input">
            </div>

            <div class="layui-input-inline">
                <div class="layui-btn layui-btn-normal layui-btn-sm" onclick="sel_time(-1)">前一天</div>
                <div class="layui-btn layui-btn-normal layui-btn-sm" onclick="sel_time(1)">后一天</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">有无手机</label>
            <div class="layui-input-block">
                <input type="radio" name="sel_is_mobile" value="" title="全部">
                <input type="radio" name="sel_is_mobile" value="1" title="有">
                <input type="radio" name="sel_is_mobile" value="0" title="无">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo">查找</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>

    <!-- 数据表格 -->
    <table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>

    <script type="text/html" id="head_toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm" lay-event="search">检索</button>
            <button class="layui-btn layui-btn-sm" lay-event="getCheckData">查看选中数据</button>
            <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">批量删除</button>
        </div>
    </script>

    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="del">删除</button>
        </div>
    </script>

    <script>
        var form = layui.form, table = layui.table, layer = layui.layer, $ = layui.$, laydate = layui.laydate;
        // $.get('/kh_info/kh_info/get_kh_info', function ())
        var form_table = table.render({
            defaultToolbar: ['exports', 'print'],
            elem: '#formfields_table',
            height: "700px",
            limit: 20,
            limits: [20, 50, 100, 200, 500],
            page: true,
            // totalRow: true,//开启合计
            url: '/kh_info/kh_info/get_kh_info',
            toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
            method: 'post',

            cols: [[
                { type: 'checkbox' },
                { field: 'Id', title: 'Id', hide: true },
                { field: 'mobile', title: '访客手机', width: '8%' },
                { field: 'device', title: '使用设备', width: '8%' },
                { field: 'guest_id', title: '访客ID', width: '8%' },
                { field: 'guest_name', title: '访客名称', width: '8%' },
                { field: 'guest_ip_info', title: 'ip信息', width: '8%' },
                { field: 'guest_ip', title: 'ip地址', width: '8%' },
                { field: 'time', title: '时间', width: '8%' },
                { field: 'se', title: '搜索引擎', width: '8%' },
                { field: 'kw', title: '关键词', width: '8%' },
                { field: 'from_page', title: '来源页面', width: '8%' },
                { field: 'land_page', title: '着陆页面', width: '8%' },
                { field: 'talk_page', title: '咨询页面', width: '8%' },
                // { field: 'company_id', title: '公司ID', width: '8%' },
                // { field: 'id6d', title: '客服id', width: '8%' },
                { field: 'worker_id', title: '客服工号', width: '8%' },
                { field: 'remark', title: '备注', width: '8%' },
                // { field: 'tag', title: '访客标签', width: '8%' },
                { field: 'weixin', title: '微信', width: '8%' },
                { field: 'qq', title: 'QQ', width: '8%' },
                // { field: 'email', title: '邮箱', width: '8%' },
                // { field: 'address', title: '地址', width: '8%' },
                // { field: 'province', title: '省份', width: '8%' },
                // { field: 'city', title: '城市', width: '8%' },
                // { field: 'zipcode', title: '邮编', width: '8%' },
                { field: '', title: '操作', toolbar: '#toolbar', width: '8%', fixed: 'right' }
            ]],
            done: function (res, curr, count) {
                // let i = 0;
                // $.each(res.data, function (k, v) {
                //     if (k = 'time') {
                //         console.log(res.data[i][k]);
                //     }
                //     i++;
                // });
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
                case 'getCheckData':
                    // console.log(checkStatus)
                    var data = checkStatus.data;
                    layer.alert(JSON.stringify(data));
                    break;
                case 'dels':
                    let ids = [];
                    checkStatus.data.forEach(index => {
                        ids.push(index.Id);
                    });
                    if (ids.length > 0) {
                        layer.confirm('确定删除？', function (index) {
                            $.post('/kh_info/kh_info/dels_kh_info', { ids: ids }, function (res) {
                                if (res.code == 0) {
                                    layer.msg(res.msg, { time: 2000 })
                                    form_table.reload();
                                } else {
                                    layer.alert(res.msg);
                                }
                            }, 'json');
                            layer.close(index);
                        });
                    }
                    break;
            }
        })

        //监听行工具事件
        table.on('tool(lay_formfields)', function (obj) {
            switch (obj.event) {
                case 'del':
                    layer.confirm('确定删除？', function (index) {
                        // console.log(JSON.stringify(obj.data));
                        $.get('/kh_info/kh_info/del_kh_info?id=' + obj.data.Id, function (res) {
                            if (res.code == 0) {
                                layer.msg(res.msg, { time: 2000 })
                                obj.del();
                            } else {
                                layer.alert(res.msg);
                            }
                        })
                        layer.close(index);
                    });
                    break;
            }
        })

        //监听提交
        form.on('submit(formDemo)', function (data) {
            // layer.alert(JSON.stringify(data.field));
            // console.log(data.field);
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