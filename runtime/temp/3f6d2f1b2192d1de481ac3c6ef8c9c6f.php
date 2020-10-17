<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:67:"D:\phpstudy_pro\WWW\public/../application/index\view\main\main.html";i:1602917746;s:20:"./static/header.html";i:1602917348;}*/ ?>
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
        <!-- <blockquote class="layui-elem-quote">
            <b>新增首页面板说明：</b>
            <br>
            昨日项目成本：成本差值，指(实际成本 减 目标成本)。0为目标成本，高于0的部分代表超出目标成本多少
            <br>
            待完成目标：默认显示未完成的目标，点击
            <span style="color: #3377ff;">目标</span> 可以添加目标，如果目标已完成就点击
            <span style="color: #3377dd;">确认完成</span>
        </blockquote> -->

        <!-- 面板内容区 -->
        <div class="layui-row panel layui-col-space10" style="margin-top:0;">

            <template v-if="hour<=11">

                <div class="layui-col-md6">
                    <div class="layui-tab layui-tab-brief layui-card project" lay-filter="docDemoTabBrief"
                        style="margin:0;">
                        <ul class="layui-tab-title">
                            <li class="layui-this">成本</li>
                            <li>成本曲线</li>
                        </ul>
                        <div class="layui-tab-content" style="height: 330px;">

                            <div class="layui-tab-item layui-show">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input" id="sel_time" placeholder="选择日期范围"
                                            readonly="" autocomplete="off">
                                    </div>
                                </div>
                                <canvas id="myProject" height="100"></canvas>
                            </div>

                            <div class="layui-tab-item">
                                <canvas id="myCostLine" height="100"></canvas>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="layui-col-md6">

                    <div class="layui-tab layui-tab-brief layui-card" lay-filter="docDemoTabBrief" style="margin:0;">
                        <ul class="layui-tab-title">
                            <li class="layui-this">消费曲线</li>
                        </ul>
                        <div class="layui-tab-content" style="height: 330px;">

                            <div class="layui-tab-item layui-show">
                                <div class="layui-form-item" style="margin-bottom: 1px;">
                                    <div class="layui-input-inline">
                                        <select name="project" id="sel_project" lay-search="" class="layui-input">
                                            <option value="">
                                                选择项目
                                            </option>
                                            <?php if(is_array($pro) || $pro instanceof \think\Collection || $pro instanceof \think\Paginator): $i = 0; $__LIST__ = $pro;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $vo['Id']; ?>"><?php echo $vo['ProjectName']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="layui-input-inline">
                                        <select name="user" id="sel_user" lay-search="" class="layui-input" <?php if(\think\Session::get('type')>2): ?> disabled="disabled" <?php endif; ?>>
                                            <option value="">
                                                <?php if(\think\Session::get('type')>2): ?> <?php echo \think\Session::get('username'); else: ?>选择用户
                                                <?php endif; ?>
                                            </option>
                                            <?php if(is_array($user) || $user instanceof \think\Collection || $user instanceof \think\Paginator): $i = 0; $__LIST__ = $user;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $vo['User_Id']; ?>"><?php echo $vo['Name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <canvas id="myConsume" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

            </template>

            <template v-if="true">

                <div class="layui-col-md6">
                    <div class="layui-tab layui-tab-brief layui-card" lay-filter="docDemoTabBrief" style="margin:0;">
                        <ul class="layui-tab-title">
                            <li class="layui-this">今日线索数量</li>
                            <li>线索数量条形图</li>
                            <li>线索曲线</li>
                        </ul>
                        <div class="layui-tab-content" style="height: 450px;overflow: auto;">

                            <div class="layui-tab-item layui-show">
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

                            <div class="layui-tab-item">
                                <canvas id="myClueNum" height="100"></canvas>
                            </div>

                            <div class="layui-tab-item">
                                <canvas id="myClueLine" height="100"></canvas>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">待完成目标</div>
                        <div class="layui-card-body" style="height: 450px;overflow: auto;">
                            <table class="layui-table" lay-skin="line">
                                <thead>
                                    <th><a v-on:click="add_target" style="color: #3377ff;cursor:pointer;">目标</a></th>
                                    <th>截止时间</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in target" :key="item.Id">
                                        <td>{{item.Work_Target}}</td>
                                        <td>{{date_tran(item.End_time)}}</td>
                                        <td>
                                            <a v-if="date_compare(item.End_time)" href="/public/index.php/target"
                                                style="color: #ff5566;">超过截止时间</a>
                                            <a v-else href="/public/index.php/target" style="color: black;">待完成</a>
                                        </td>
                                        <td><a v-on:click="finish_target(item.Id,index)"
                                                style="color: #3377dd;cursor:pointer;">确认完成</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </template>

            <template v-if="hour>11">

                <div class="layui-col-md6">
                    <div class="layui-tab layui-tab-brief layui-card project" lay-filter="docDemoTabBrief"
                        style="margin:0;">
                        <ul class="layui-tab-title">
                            <li class="layui-this">成本</li>
                            <li>成本曲线</li>
                        </ul>
                        <div class="layui-tab-content" style="height: 330px;">

                            <div class="layui-tab-item layui-show">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input" id="sel_time" placeholder="选择日期范围"
                                            readonly="" autocomplete="off">
                                    </div>
                                </div>
                                <canvas id="myProject" height="100"></canvas>
                            </div>

                            <div class="layui-tab-item">
                                <canvas id="myCostLine" height="100"></canvas>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="layui-col-md6">

                    <div class="layui-tab layui-tab-brief layui-card" lay-filter="docDemoTabBrief" style="margin:0;">
                        <ul class="layui-tab-title">
                            <li class="layui-this">消费曲线</li>
                        </ul>
                        <div class="layui-tab-content" style="height: 330px;">

                            <div class="layui-tab-item layui-show">
                                <div class="layui-form-item" style="margin-bottom: 1px;">
                                    <div class="layui-input-inline">
                                        <select name="project" id="sel_project" lay-search="" class="layui-input">
                                            <option value="">
                                                选择项目
                                            </option>
                                            <?php if(is_array($pro) || $pro instanceof \think\Collection || $pro instanceof \think\Paginator): $i = 0; $__LIST__ = $pro;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $vo['Id']; ?>"><?php echo $vo['ProjectName']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="layui-input-inline">
                                        <select name="user" id="sel_user" lay-search="" class="layui-input" <?php if(\think\Session::get('type')>2): ?> disabled="disabled" <?php endif; ?>>
                                            <option value="">
                                                <?php if(\think\Session::get('type')>2): ?> <?php echo \think\Session::get('username'); else: ?>选择用户
                                                <?php endif; ?>
                                            </option>
                                            <?php if(is_array($user) || $user instanceof \think\Collection || $user instanceof \think\Paginator): $i = 0; $__LIST__ = $user;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $vo['User_Id']; ?>"><?php echo $vo['Name']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <canvas id="myConsume" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

            </template>

        </div>
    </div>


    <script type=“text/javascript” src="/public/static/js/vue.min.js"></script>
    <script type=“text/javascript” src="/public/static/js/Chart.min.js"></script>

    <script>
        var layer = layui.layer, $ = layui.$, laydate = layui.laydate, form = layui.form

        var app = new Vue({
            el: '#app',
            data: {
                hour: new Date().getHours(),//用来判断上午和下午
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

                add_target: function (event) {
                    layer.open({
                        type: 2,
                        title: "新增目标",
                        shadeClose: true,
                        area: ['50%', '700px'],
                        content: '/public/index.php/ins_target'
                    });
                },

                //完成目标
                finish_target: function (Id, index) {
                    var that = this;
                    var index = index;
                    $.ajax({
                        url: "/public/index.php/finsh_target/?Id=" + Id,
                        data: {},
                        type: "PUT",
                        dataType: "json",
                        success: function (data) {
                            layer.msg(data.msg, { time: 1500 }, function () {
                                that.target.splice(index, 1)//删除具体索引的值
                            })
                        },
                        error: function (data) {
                            //配置一个透明的询问框
                            layer.msg('操作失败，请联系管理员', {
                                time: 5000, //5s后自动关闭
                                btn: ['确认']
                            });
                        }
                    });
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
            //获取项目面板图表的数据
            getMyProject();

            //获取消费面板图表的数据
            // getMyConsume();

            // 获取曲线的线索数据
            getMyClueData();

            //获取工作目标
            $.get('/public/index.php/get_target?limit=20&page=1', function (res) {
                // console.log(res)
                app.$data.target = res.data

                //设置目标超时提醒用的缓存
                window.localStorage.setItem('targetRemind', JSON.stringify(res.data));
            })

            //获取线索数量
            $.get('/public/index.php/get_customer_count?limit=20&page=1', function (res) {
                // console.log(res)
                app.$data.clues = res.data
            })

            $('#sel_user').change(() => {
                let project_id = $('#sel_project').val();
                let user_id = $('#sel_user').val();
                getMyClueData(project_id, user_id);
            });

            $('#sel_project').change(() => {
                let project_id = $('#sel_project').val();
                let user_id = $('#sel_user').val();
                getMyClueData(project_id, user_id);
            })

        });

        //项目成本面板的图表
        var ctx = document.getElementById("myProject").getContext('2d');
        var ProjectData = {};
        var myProject = new Chart(ctx, {
            type: 'bar',
            data: ProjectData,
            options: {}
        });

        //消费数据面板
        var ctx2 = document.getElementById("myConsume").getContext('2d');
        var consumeData = {};
        var myConsume = new Chart(ctx2, {
            type: 'line',
            data: consumeData,
            options: {}
        });

        //线索数量条形图
        var ctx3 = document.getElementById("myClueNum").getContext('2d');
        var clueNumData = {};
        var myClueNum = new Chart(ctx3, {
            type: "bar",
            data: clueNumData,
            options: {}
        });

        //成本折现图
        var ctx4 = document.getElementById("myCostLine").getContext('2d');
        var costLineData = {};
        var myCostLine = new Chart(ctx4, {
            type: "line",
            data: costLineData,
            options: {}
        });

        //线索曲线
        var ctx5 = document.getElementById("myClueLine").getContext('2d');
        var clueLineData = {};
        var myClueLine = new Chart(ctx5, {
            type: "line",
            data: clueLineData,
            options: {}
        });


        // 获取项目成本面板图表的数据 date是日期范围
        function getMyProject(date = "") {
            // console.log(myProject.data)
            let url = '/public/index.php/get_my_project'
            if (date != "") {
                url = '/public/index.php/get_my_project?sel_date=' + date;
            }
            $.get(url, function (res) {
                // console.log(res)
                //项目成本的数据
                var ProjectData = {
                    labels: [],
                    datasets: [{
                        label: '平均成本-目标成本',
                        data: [],
                        backgroundColor: [],
                        borderColor: [],
                        borderWidth: 1
                    }]
                }
                //写入数据
                res.forEach(element => {
                    // console.log(element)
                    ProjectData.labels.push(element.ProjectName)
                    ProjectData.datasets[0].data.push(element.Diff.toFixed(2))
                    //设置随机颜色
                    let rgba = getRandColor(0.3)
                    ProjectData.datasets[0].backgroundColor.push(rgba)
                    ProjectData.datasets[0].borderColor.push('rgba(150,150,150,1)')
                });

                myProject.data = ProjectData
                myProject.update();


                //顺便写入线索数量条形图的数据
                var clueNumData = {
                    labels: [],
                    datasets: [{
                        label: '(实际数量/天数)-目标数量',
                        backgroundColor: [],
                        borderColor: [],
                        data: [],
                        borderWidth: 1
                    }]
                }
                //写入数据
                res.forEach(element => {
                    // console.log(element)
                    clueNumData.labels.push(element.ProjectName)
                    clueNumData.datasets[0].data.push(element.ClueDiff.toFixed(2))
                    //设置随机颜色
                    let rgba = getRandColor(0.3)
                    clueNumData.datasets[0].backgroundColor.push(rgba)
                    clueNumData.datasets[0].borderColor.push('rgba(150,150,150,1)')
                });

                myClueNum.data = clueNumData
                myClueNum.update();
            })
        }

        // 获取消费面板图表的数据
        function getMyConsume(user_id = "") {
            let url = '/public/index.php/get_my_consume/?user_id=' + user_id

            $.get(url, function (res) {
                var consumeData = res
                myConsume.data = consumeData
                myConsume.update();
            })
        }

        // 获取成本和线索曲线的数据
        function getMyClueData(project_id = "", user_id = "") {
            let url = '/public/index.php/get_my_cluedata/?project_id=' + project_id + '&user_id=' + user_id

            $.get(url, function (res) {
                // console.log(res)

                // 消费曲线
                var consumeData = {
                    labels: res.labels,
                    datasets: res.conData
                }
                myConsume.data = consumeData;
                myConsume.update();

                //线索曲线
                var clueLineData = {
                    labels: res.labels,
                    datasets: res.clueData
                }
                myClueLine.data = clueLineData;
                myClueLine.update();

                //成本曲线
                var costLineData = {
                    labels: res.labels,
                    datasets: res.costData
                }
                myCostLine.data = costLineData;
                myCostLine.update();
            })
        }

    </script>

    <!-- 这是时间范围选择功能的内容，注意这是扩展后的laydate.js与原版不同 -->
    <!-- <script src="/static/layui/lay/modules/laydate.js"></script> -->
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
                getMyProject(value)
            }
        });
    </script>

</body>