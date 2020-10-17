<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:74:"D:\phpstudy_pro\WWW\public/../application/index\view\main\main_client.html";i:1597728226;s:20:"./static/header.html";i:1602917348;}*/ ?>
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
    <style>
        .panel {
            background-color: #eeeeee;
            margin-top: 20px;
            /* margin-bottom: 20px; */
            padding: 20px 10px;
        }

        .layui-card-header {
            border-bottom: #eeeeee solid 1px;
        }

        .foot {
            background-color: #eeeeee;
            height: 50px;
            line-height: 50px;
            padding-left: 10px;
            /* width: 100%; */
        }
    </style>

    <div class="layui-fluid" id="app">
        <blockquote class="layui-elem-quote">
            <b>新增首页面板说明：</b>
            <br>
            线索数量面板显示今日线索数量（未去除重复）仅供参考
            <br>
        </blockquote>


        <!-- 面板内容区 -->
        <div class="layui-row panel layui-col-space10">

            <div class="layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">今日线索数量</div>
                    <div class="layui-card-body" style="height: 250px;overflow: auto;">
                        <table class="layui-table" lay-skin="line">
                            <thead>
                                <th>客户</th>
                                <th>留言</th>
                                <th>推送</th>
                                <th>飞鱼线索</th>
                                <th>总数</th>
                            </thead>
                            <tbody>
                                <tr v-for="item in clues">
                                    <td>{{item.Name}}</td>
                                    <td>{{item.Customer}}</td>
                                    <td>{{item.Push}}</td>
                                    <td>{{item.Clues}}</td>
                                    <td>{{Number(item.Customer)+Number(item.Push+item.Clues)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>


    <script src="static/js/vue.min.js"></script>
    <script src="static/js/Chart.min.js"></script>

    <script>
        var layer = layui.layer, $ = layui.$, laydate = layui.laydate, form = layui.form

        var app = new Vue({
            el: '#app',
            data: {
                target: Object,
                clues: Object
            },
            methods: {
                //转化日期时间
                date_tran: function (date) {
                    var time = new Date(date)
                    let m = time.getMonth() + 1
                    let d = time.getDate()
                    let h = time.getHours()
                    let i = time.getMinutes()
                    if (i < 10) {
                        i = '0' + String(i)
                    }
                    return m + '月' + d + '日 ' + h + ':' + i
                },
                //时间比较,超时返回true,未超时返回false
                date_compare: function (date) {
                    var time = new Date(date).getTime()
                    var time2 = new Date().getTime()
                    if (time2 > time) {
                        return true;
                    } else {
                        return false;
                    }
                },
            }
        })

        //获取随机颜色
        function getRandColor(tran = 1) {
            //传入透明度
            let r = Math.floor((Math.random() * 255) + 1)
            let g = Math.floor((Math.random() * 255) + 1)
            let b = Math.floor((Math.random() * 255) + 1)
            return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + tran + ')'
        }

        $(document).ready(function () {

            //获取线索数量
            $.get('/get_customer_count?limit=20&page=1', function (res) {
                // console.log(res)
                app.$data.clues = res.data
            })

            $.get('get_clue_conut?limit=20',function(res){
                console.log(res);
            })

        });

    </script>

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
            ],
            done: function (value, date) {
                //选中后的回调
            }
        });
    </script>
</body>