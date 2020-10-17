<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:84:"D:\phpstudy_pro\WWW\public/../application/client_rec\view\client_rec\client_rec.html";i:1597728233;s:20:"./static/header.html";i:1602917348;s:27:"./static/js/selectdate.html";i:1602901063;}*/ ?>
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

            <?php endif; ?>

    <div class="layui-col-xs4 layui-col-sm3 layui-col-md4 layui-col-lg4">

    <div class="layui-inline">

      <div class="layui-input-inline">

        <input type="text"  class="layui-input" id="sel_time" placeholder="请选择日期范围">

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

				<button class="layui-btn layui-btn-sm laymy-w1" id="search">搜索</button>

				</div>

			</div>

			</div>

		</div>

		</div>

		<table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>

	<?php if(\think\Session::get('auth')<3): ?>  <script type="text/html" id="head_toolbar">

			<div class="layui-btn-container">
			
				<button class="layui-btn layui-btn-sm" lay-event="insert">新增打款记录</button>
			
			</div>
			
			</script>
			
			<script type="text/html" id="toolbar">

				<div class="layui-btn-container">
		
					<button class="layui-btn layui-btn-sm" lay-event="update">编辑</button>
		
					<button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">删除</button>
		
				</div>
						
			</script>

     <?php endif; ?>


	<!-- 导入日期选择扩展 -->
	<!-- 这是时间范围选择功能的内容，注意这是扩展后的laydate.js与原版不同 -->
<script src="/public/static/layui/lay/modules/laydate.js"></script>
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
                console.log(nowdate);
            }
            console.log(nowdate);
            //新的时间
            var newdate = new Date(nowdate.setDate(nowdate.getDate() - 1));
            console.log(newdate);
            
            var str = formatDate(newdate, 'yyyy-MM-dd');
        //    console.log(str);
            $('#sel_time').val(str + ' - ' + str);
            return false;
        });
        $('#nextday').click(function () {
            var nowdate = $('#sel_time').val();
            if (nowdate.length < 10) {
                nowdate = new Date();
            } else {
                nowdate = new Date(nowdate.substr(13, 10));
                console.log(nowdate);
            }
            console.log(nowdate);
            var newdate = new Date(nowdate.setDate(nowdate.getDate() + 1));
            var str = formatDate(newdate, 'yyyy-MM-dd');
            // console.log(str);
            $('#sel_time').val(str + ' - ' + str);
            return false;
        })

    });
</script>
	 <script>

		var table = layui.table,layer = layui.layer,$=layui.$,laypage=layui.laypage

		var form_table = table.render({
			elem:'#formfields_table',
			height:"full-70",
			limit:20,
            limits:[20,50,100,200,500],
			page: true,
			totalRow: true,
			toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
            defaultToolbar:['exports','print'],
			url:'/get_client_rec',
			method:'get',
			cols:[[
				{field:'Id',title:'ID',hide:true},
                <?php if(\think\Session::get('auth')<3): ?>	{field:'Name',title:'客户',totalRowText: '合计',sort:true},<?php endif; ?>
				{field:'Money',title:'金额',sort:true,totalRow: true,},
				{field:'Suc_time',title:'到账时间',sort:true},
				{field:'Cre_time',title:'录入时间',sort:true},
				{field:'Remarks',title:'备注',sort:true},
                <?php if(\think\Session::get('auth')<3): ?>	{field:'',title:'操作',toolbar:'#toolbar'}<?php endif; ?>
			]],

			done:function(res, curr, count){
			}
		});

	//将上述表格示例导出为 csv 文件



	//监听头部工具行
	table.on('toolbar(lay_formfields)', function (obj) {

		var data = obj.data;
		var event = obj.event;
		var tr = obj.tr;

		switch (event) {
			case 'insert': {
				layer.open({
					type: 2,
					title: "新增推广账号",
					shadeClose: true,//外部关闭
					area: ['50%', '600px'],
					content: '/ins_client_rec'
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
			case 'dels': {
				layer.confirm('是否确定删除？', { btn: ['确定', '取消'] }, function () {
					$.post('/dels_client_rec_do', { Id: data.Id }, function (data) {
						layer.msg(data.msg, function () {
							if (data.code === 0) {
								form_table.reload();
							}
						})
					}, 'json');
				});
				break;
			}
			case 'update': {
				layer.open({
					type: 2,
					title: "编辑充值记录",
					shadeClose: true,//外部关闭
					area: ['80%', 600],
					content: '/upd_client_rec?Id=' + obj.data.Id
				});
				break;
			}
		}
	});

	//搜索
	$("#search").click(function () {
		var sel_client_id = $("#sel_client_id").val() || "";
		var sel_time = $('#sel_time').val() || "";
		form_table.reload({	//重载表格
			where: {
				sel_client_id: sel_client_id,
				sel_time: sel_time,
			}
		});
	});



</script>

</body>