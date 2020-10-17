<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:65:"D:\phpstudy_pro\WWW\public/../application/wxapi\view\gzh\gzh.html";i:1597728195;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
			<?php if(\think\Session::get('type')<3): ?>
			<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
				<div class="layui-inline">
					<div class="layui-input-inline">
						<select id="sel_client_id" class="layui-select">
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
						<select id="sel_user_id" lay-verify="required" lay-search="" class="layui-select">
							<option value="">请选择负责人</option>
							<?php foreach($per as $k=>$v): ?>
							<option value="<?php echo $v['User_Id']; ?>"><?php echo $v['Name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
				<div class="layui-inline">
					<div class="layui-input-inline">
						<select id="sel_status" lay-verify="required" lay-search="" class="layui-select">
							<option value="1">正常</option>
							<option value="0">停用</option>
						</select>
					</div>
				</div>
			</div>
			<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
				<div class="layui-inline">
					<button class="layui-btn layui-btn-sm laymy-w1" id="search">搜索</button>
				</div>
			</div>
		</div>
		
	</div>
	<table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>
	<script type="text/html" id="toolbar">
		<div class="layui-btn-container">
          <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="del">解绑</button>
		</div>
	  </script>

	<script>

		var table = layui.table, layer = layui.layer, $ = layui.$, laydate = layui.laydate;
		var myDate = new Date();
		var date = myDate.getDate();
		var h = myDate.getHours();       //获取当前小时数(0-23)
		var m = myDate.getMinutes();     //获取当前分钟数(0-59)
		var s = myDate.getSeconds();

		var form_table = table.render({
			elem: '#formfields_table',
			height: "full-70",
			limit: 20,
			limits: [20, 50, 100, 200, 500],
			page: true,
			totalRow: true,//开启合计
			toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
			defaultToolbar: ['exports', 'print'],
			url: '/wxapi/gzh/get_user',
			method: 'get',
			cols: [[
				{ field: 'User_Id', title: 'User_Id', hide: true },
				{ field: 'Name', title: '姓名'},
				{ field: 'Alias', title: '别名'},
				{ field: 'Nickname', title: '微信昵称'},
				{ field: 'OpenID', title: '微信OpenID'},
				{ field: 'Remark', title: '微信备注'},
				{ field: '', title: '操作', toolbar: '#toolbar' }
			]],
			done: function (res, curr, count) {
			}

		});

		//监听列工具行
		table.on('tool(lay_formfields)', function (obj) {
			var data = obj.data;
			var event = obj.event;
			var tr = obj.tr;
			switch (event) {
				case 'del':
					{
						layer.confirm('解除该账号绑定的微信将使该账号无法操作相关功能，是否确定解除绑定？', { btn: ['确定', '取消'] }, function () {
    						$.post('/wxapi/gzh/unbindwx', { userid: data.User_Id , openid: OpenID}, function (data) {
    							layer.msg(data.msg, function () {
    								if (data.code === 0) {
    									form_table.reload();
    								}
    							})
    						}, 'json');
						});
						break;
					}
			}
		});
		//搜索
		$("#search").click(function () {
			var sel_client_id = $("#sel_client_id").val() || "";
			var sel_user_id = $('#sel_user_id').val() || "";
			var sel_status = $('#sel_status').val() || "";
			form_table.reload({	//重载表格
				where: {
					sel_status: sel_status,
					sel_client_id: sel_client_id,
					sel_user_id: sel_user_id
				}
			});
		});

		//时间比较
		function CompareDate(t1, t2, t3) {
			var date = new Date();
			var a = t1.split(":");
			var b = t2.split(":");
			var c = t3.split(":");
			return (date.setHours(a[0], a[1]) < date.setHours(b[0], b[1]) && date.setHours(b[0], b[1]) < date.setHours(c[0], c[1]));
		}
	</script>
</body>