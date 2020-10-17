<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:81:"D:\phpstudy_pro\WWW\public/../application/promotion\view\promotion\promotion.html";i:1602729745;s:20:"./static/header.html";i:1602579388;}*/ ?>
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

		<div class="layui-row layui-form-item">
		    
		    <div class="layui-col-xs4 layui-col-sm3 layui-col-md1 layui-col-lg1">
				<div class="layui-inline">
				  <div class="layui-input-inline">
					<select id="sel_platform" lay-verify="required" lay-search="" class="layui-select">
						<option value="">请选择平台</option>
						<?php if(is_array($platform) || $platform instanceof \think\Collection || $platform instanceof \think\Paginator): $i = 0; $__LIST__ = $platform;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
							<option value="<?php echo $vo['Platform']; ?>"><?php echo $vo['Platform']; ?></option>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
				  </div>
				</div>
			</div>
			
			<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
				<div class="layui-inline">
				  
				  <div class="layui-input-inline" style="width: 70px;">
					<input type="text" name="sel_SumMon_min" id="sel_SumMon_min" placeholder="￥" autocomplete="off" class="layui-input">
				  </div>
				  <div class="layui-form-mid">-</div>
				  <div class="layui-input-inline" style="width: 70px;">
					<input type="text" name="sel_SumMon_max" id="sel_SumMon_max" placeholder="￥" autocomplete="off" class="layui-input">
				  </div>
				</div>
			</div>
			
			<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
				<div class="layui-inline">
					<div class="layui-input-inline" style="width: 100px;">
						<input type="text" name="sel_SumCon7" id="sel_SumCon7" placeholder="近7日均消费" autocomplete="off" class="layui-input">
					</div>
				</div>
			</div>

		<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">

    <div class="layui-inline">

      <div class="layui-input-inline">

         <input type="text" class="layui-input" id="sel_remarks" placeholder="请输入备注关键字">

      </div>

    </div>

    </div>

    <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">

    <div class="layui-inline">

      <div class="layui-input-inline">

 <input type="text" class="layui-input" id="sel_pro_user" placeholder="请输入账号关键字">

      </div>

    </div>

    </div>

    <div class="layui-col-xs4 layui-col-sm2 layui-col-md1 layui-col-lg1">

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

		      <script type="text/html" id="head_toolbar">

		<div class="layui-btn-container">

          <button class="layui-btn layui-btn-sm" lay-event="insert">新增推广账号</button>



		</div>

      </script>

	  <?php if(\think\Session::get('type')!=6): ?>
	 <script type="text/html" id="toolbar">
		
		<div class="layui-btn-container">

          <button class="layui-btn layui-btn-sm" lay-event="rec">充值</button>

          <button class="layui-btn layui-btn-sm" lay-event="update">编辑</button>

  		  <!-- <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="del">停用/启用</button> -->

		  {{#  if(d.Status == 1){ }}
		  	<button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="del">停用</button>
		  {{#  } else { }}
			<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="del">启用</button>
		  {{#  } }}

          <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">删除</button>

		</div>
		
	  </script>
	  <?php endif; ?>



	 <script>



		var table = layui.table,layer = layui.layer,$=layui.$,laydate = layui.laydate;

		var form_table = table.render({

			elem:'#formfields_table',

			height:"full-70",

			limit:20,

            limits:[20,50,100,200,500],

			page: true,

            totalRow: true,//开启合计

			toolbar:'#head_toolbar',

            defaultToolbar:['exports','print'],

			url:'/public/index.php/get_promotion',

			method:'get',

			cols:[[

				{field:'Id',title:'ID',hide:true},

				{field:'Remarks',title:'账号备注',totalRowText: '合计',width:'10%',fixed: 'left'},

				{field:'Pro_User',title:'推广账号',width:'10%',fixed: 'left'},

				{field:'Pro_Psw',title:'推广密码',width:'10%'},
				
				{field:'Platform',title:'平台',width:'8%'},
				
				{field:'Domain',title:'域名',width:'8%'},

				{field:'Rebate',title:'平台返点',width:'8%'},

				{field:'SumRec',title:'充值总金额',sort:true,totalRow: true,width:'10%'},

				{field:'SumCon',title:'消费总金额',sort:true,totalRow: true,width:'10%'},

                {field:'SumMon',title:'账号总余额',sort:true,totalRow: true,width:'10%'},

				{field:'SumCon7',title:'近7天日均消费',sort:true,totalRow: true,width:'10%'},

				{field:'Day',title:'余额可消费天数',sort:true,width:'10%'},

				{field:'Status',title:'状态',hide:true},
				
				<?php if(\think\Session::get('type')!=6): ?>
				{field:'',title:'操作',toolbar:'#toolbar',width:260 ,fixed: 'right'}
				<?php endif; ?>

			]],

            done:function(res, curr, count){

                $.each(res.data,function(k,v){

                    var summon=v.SumMon

                    var sumcon7=v.SumCon7

                    if(sumcon7>summon){

                        $(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#CEECF5");

                    }

                })

                var pro = $('td[data-field="Pro_Psw"]')
    				pro.dblclick(function(e){
    					// console.log($(e.target).parent().siblings('td[data-field="Pro_Id"]').text());
    					var psw = $(e.target)
    					var Pro_Id = psw.parent().siblings('td[data-field="Id"]').text()
    					$.post('/public/index.php/get_pro_key',{Pro_Id:Pro_Id},function(res){
    						if(res.code == 0){psw.text(res.key)}
    					},'json')
    				})
    
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

			            title:"新增推广账号",

			            shadeClose:true,//外部关闭

			            area:['50%','600px'],

						  content: '/public/index.php/ins_promotion'

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

				case 'rec':

				{

						layer.open({

							  type: 2,

				              title:"推广账号充值",

				              area:['80%',600],

				              shadeClose:true,//外部关闭

							  content: '/public/index.php/ins_promotion_rec?Id='+obj.data.Id

							});

						break;

				}

				case 'del':

				{

						$.post('/public/index.php/del_promotion_do',{Id:data.Id},function(data){

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

					layer.confirm('是否确定删除？',{btn:['确定','取消']},function(){

						$.post('/public/index.php/dels_promotion_do',{Id:data.Id},function(data){

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

			              title:"编辑推广账号",

			              shadeClose:true,//外部关闭

			              area:['80%',600],

						  content: '/public/index.php/upd_promotion?Id='+obj.data.Id

						});

					break;

				}

			}

		});

        //搜索

		$("#search").click(function(){

			var sel_pro_user = $("#sel_pro_user").val()||"";
			var sel_remarks= $('#sel_remarks').val()||"";
			var sel_status = $('#sel_status').val()||"";
			var sel_platform = $("#sel_platform").val()||"";
			var sel_SumMon_min = $('#sel_SumMon_min').val()||"";
			var sel_SumMon_max = $('#sel_SumMon_max').val()||"";
			var sel_SumCon7 = $('#sel_SumCon7').val()||"";

			form_table.reload({	//重载表格
				where:{
					sel_pro_user:sel_pro_user,
					sel_remarks:sel_remarks,
					sel_status:sel_status,
					sel_platform:sel_platform,
					sel_SumMon_min:sel_SumMon_min,
					sel_SumMon_max :sel_SumMon_max,
					sel_SumCon7:sel_SumCon7
				}
			});
		});

	 </script>

	 </body>