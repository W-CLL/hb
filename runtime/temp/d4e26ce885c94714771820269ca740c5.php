<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:68:"D:\phpstudy_pro\WWW\public/../application/target\view\meal\meal.html";i:1597728206;s:20:"./static/header.html";i:1602579388;s:27:"./static/js/selectdate.html";i:1597727918;}*/ ?>
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

            <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select id="sel_user_id" lay-verify="required" lay-search="" class="layui-select">
                            <option value="">请选择负责人</option>
                            <?php foreach($per as $k=>$v): ?>
                            <option value="<?php echo $v['User_Id']; ?>"><?php echo $v['Name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="layui-col-xs4 layui-col-sm3 layui-col-md4 layui-col-lg4">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" id="sel_time" placeholder="请选择日期范围" value="">
                        <script>
                            //设置默认的时间
                            function formatDate01(date) { //设置时间转换格式
                                var y = date.getFullYear();  //获取年
                                var m = date.getMonth() + 1;  //获取月
                                m = m < 10 ? '0' + m : m;  //判断月是否大于10
                                var d = date.getDate();  //获取日
                                d = d < 10 ? ('0' + d) : d;  //判断日期是否大10
                                return y + '-' + m + '-' + d;  //返回时间格式
                            }
                            $defaultTime = formatDate01(new Date())
                            $('#sel_time').val($defaultTime + ' - ' + $defaultTime);
                        </script>
                    </div>
                    <button class="layui-btn layui-btn-normal layui-btn-sm" id="lastday">前一天</button>
                    <div class="layui-input-inline">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" id="nextday">后一天</button>
                    </div>
                </div>
            </div>

            <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select id="sel_lunch" lay-verify="required" lay-search="" class="layui-select">
                            <option value="">午餐</option>
                            <option value="0">不需要</option>
                            <option value="1">需要</option>
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

        </div>

    </div>

    <table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>

    <script type="text/html" id="head_toolbar">
		<div class="layui-btn-container">
          <button class="layui-btn layui-btn-sm" lay-event="insert">新增订餐</button>
		</div>
    </script>

    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm" lay-event="update">编辑</button>
            <?php if(\think\Session::get('auth')<3): ?>
            <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">删除</button>
            <?php endif; ?>
        </div>
    </script>

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

        var table = layui.table, layer = layui.layer, $ = layui.$, laypage = layui.laypage;
        var form_table = table.render({
            elem: '#formfields_table',
            height: "full-70",
            limit: 20,
            limits: [20, 50, 100, 200, 500],
            page: true,
            totalRow: true,//开启合计
            toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
            defaultToolbar: ['exports', 'print'],
            url: '/get_meal',
            method: 'get',
            cols: [[
                { field: 'Id', title: 'Id', hide: true },
                { field: 'Name', title: '负责人', sort: true, totalRowText: '合计' },
                { field: 'Lunch', title: '午餐', sort: true, totalRow: true },
                { field: 'Dinner', title: '晚餐', sort: true, totalRow: true },
                { field: 'Update_time', title: '更新时间', sort: true },
                // { field: 'Complete_time', title: '完成时间', sort: true },
                { field: 'Remarks', title: '备注', sort: true },
                { field: '', title: '操作', toolbar: '#toolbar' }
            ]],
            done: function (res, curr, count) {
                console.log(res.data);
                $.each(res.data, function (k, v) {
                    var status = v.Status;
                    var endTime = new Date(v.End_time);
                    var nowTime = new Date();
                    //截止时间未完成添加背景色
                    if (nowTime.getTime() > endTime.getTime() && status == 1) {
                        $(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#ee8888");
                    }
                })
            }
        });

        //监听头部工具行
        table.on('toolbar(lay_formfields)', function (obj) {
            var data = obj.data;
            var event = obj.event;
            var tr = obj.tr;
            switch (event) {
                case 'insert':
                    {
                        layer.open({
                            type: 2,
                            title: "新增订餐",
                            area: ['50%', '700px'],
                            content: '/ins_meal'
                        });
                        break;
                    }
            }
        });
        //监听列工具行
        table.on('tool(lay_formfields)', function (obj) {
            console.log(obj.data)
            var data = obj.data;
            var event = obj.event;
            switch (event) {
                case 'update': {
                    layer.open({
                        type: 2,
                        title: "编辑订单信息",
                        area: ['80%', 600],
                        content: '/upd_meal?Id=' + data.Id
                    });
                    break;
                }
                case 'dels': {
                    layer.confirm('注意删除操作”不可恢复“，是否确定删除？', { btn: ['确定', '取消'], anim: 6, icon: 0 }, function () {
                        $.ajax({
                            url: "/del_meal_do/?Id=" + data.Id,
                            data: {},
                            type: "delete",
                            dataType: "json",
                            success: function (data) {
                                layer.msg(data.msg, function () {
                                    if (data.code === 0) {
                                        form_table.reload();
                                    }
                                })
                            },
                            error: function (data) {
                                //配置一个透明的询问框
                                layer.msg('删除失败,请联系管理员', {
                                    time: 5000, //5s后自动关闭
                                    btn: ['确认']
                                });
                            }
                        });
                    });
                    break;
                }
            }

        });

        //搜索
        $("#search").click(function () {
            var sel_user_id = $("#sel_user_id").val() || "";
            var sel_time = $('#sel_time').val() || "";
            var sel_lunch = $('#sel_lunch').val() || "";
            form_table.reload({	//重载表格
                where: {
                    sel_user_id: sel_user_id,
                    sel_time: sel_time,
                    sel_lunch: sel_lunch,
                }
            });
        });

    </script>

</body>