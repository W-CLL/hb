<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"D:\phpstudy_pro\WWW\public/../application/user\view\user\user.html";i:1602664916;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
        <select id="sel_type_id"   class="layui-select">
          <option value="">请选择账号类型</option>
          <?php foreach($type_name as $k=>$v): ?>
          <option value="<?php echo $v['Type_Id']; ?>"><?php echo $v['Type_Name']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    </div>
    <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
    <div class="layui-inline">
      <div class="layui-input-inline">
        <select id="sel_status" lay-verify="required" lay-search="" class="layui-select">
          <option value="">全部状态</option>
          <option value="1">正常</option>
		  <option value="0">停用</option>
		  <option value="2">隐藏</option>
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
		      <script type="text/html" id="head_toolbar">
		<div class="layui-btn-container">
          <button class="layui-btn layui-btn-sm" lay-event="insert">新增账号</button>

		</div>
      </script>
	 <script type="text/html" id="toolbar">
		<div class="layui-btn-container">
		  <button class="layui-btn layui-btn-sm" lay-event="update">编辑</button>

		  {{#  if(d.Status == 1){ }}
		  <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="stop">停用</button>
		  {{#  } else{ }}
		  <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="start">启用</button>
		  {{#  } }}

		  {{#  if(d.Status == 2){  }}
		  <button class="layui-btn layui-btn-sm layui-btn-disabled">隐藏</button>
		  {{#  }else{  }}
		  <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="hide">隐藏</button>
		  {{# } }}
		  
          <button class="layui-btn layui-btn-sm" lay-event="reset">修改账号</button>
          <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">删除</button>
        </div>
	  </script>

	 <script>
		var table = layui.table,layer = layui.layer,$=layui.$,laydate = layui.laydate;
		var form_table = table.render({
			elem:'#formfields_table',
			height:"full-70",
			limit:20,
			limits:[20,50,100,200,500],
			page: true,
			toolbar:'#head_toolbar',
            defaultToolbar:['exports','print'],
			url:'/public/index.php/get_user',
			method:'get',
			cols:[[
				{field:'Id',title:'ID',width:'50'},
				{field:'Name',title:'姓名',width:'8%',sort:true},
				{field:'User',title:'账号',width:'8%'},
				{field:'Type_Name',title:'账号类型',width:'100'},
				{field:'Auth_Name',title:'权限级别',width:'100'},
				{field:'Login_time',title:'最新登录时间',width:'160',sort:true},
				{field:'Ip',title:'最新登录IP',width:'140',sort:true},
				{field:'Phone',title:'电话',width:'8%'},
				{field:'Alias',title:'别名',width:'5%'},
				{field:'Ekey',title:'外部密钥',width:'17%'},
				{field:'NStatus',title:'状态',width:'80'},
                {field:'Msg_service',title:'短信状态',width:'5%'},
				{field:'Status',hide:true},
				{field:'',title:'操作',toolbar:'#toolbar',width:'320'}
			]],
			done:function(res, curr, count){
			}

		});
		//监听头部工具行
		table.on('toolbar(lay_formfields)',function(obj){
			var data = obj.data;
			var event = obj.event;
			var tr = obj.tr;
			switch(event)
			{
				case 'insert':
				{
					layer.open({
						  type: 2,
			            title:"新增账号",
			            shadeClose:true,//外部关闭
			            area:['50%','600px'],
						  content: '/public/index.php/ins_user'
						});

						break;
				}

			}
		});

		//监听列工具行
		table.on('tool(lay_formfields)',function(obj){
			var data = obj.data;
			var event = obj.event;
			var tr = obj.tr;
			switch(event)
			{
				case 'reset':
				{
					layer.open({
						  type: 2,
			            title:"修改账号",
			            shadeClose:true,//外部关闭
			            area:['50%','600px'],
						  content: '/public/index.php/reset_user?Id='+obj.data.Id
						});
					break;
				}
				case 'start':
				{
					$.post('/public/index.php/del_user_do',{Id:data.Id,Status:1},function(data){
						layer.msg(data.msg,function(){
							if(data.code === 0)
							{
								form_table.reload();
							}
						})
					},'json');
					break;
				}
				case 'stop':
				{
					$.post('/public/index.php/del_user_do',{Id:data.Id,Status:0},function(data){
						layer.msg(data.msg,function(){
							if(data.code === 0)
							{
								form_table.reload();
							}
						})
					},'json');
					break;
				}
				case 'hide':
				{
					$.post('/public/index.php/del_user_do',{Id:data.Id,Status:2},function(data){
						layer.msg(data.msg,function(){
							if(data.code === 0)
							{
								form_table.reload();
							}
						})
					},'json');
					break;
				}
				case 'dels':
				{
					layer.confirm('是否确定永久删除？',{btn:['确定','取消']},function(){
						$.post('/public/index.php/dels_user_do',{Id:data.Id},function(data){
							layer.msg(data.msg,function(){
								if(data.code === 0)
								{
									form_table.reload();
								}
							})
						},'json');
					});
					break;
				}
				case 'update':
				{
					layer.open({
						  type: 2,
			              title:"编辑账号",
			              shadeClose:true,//外部关闭
			              area:['80%',600],
						  content: '/public/index.php/upd_user?Id='+obj.data.Id
						});
					break;
				}
			}
		});
        //搜索
		$("#search").click(function(){
			var sel_type_id = $("#sel_type_id").val()||"";
			var sel_status = $('#sel_status').val()||"";
			form_table.reload({	//重载表格
				where:{
					sel_status:sel_status,
					sel_type_id:sel_type_id,
				}
			});
		});
	 </script>
	 </body>