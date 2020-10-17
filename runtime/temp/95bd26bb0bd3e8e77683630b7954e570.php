<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:75:"D:\phpstudy_pro\WWW\public/../application/project\view\project\project.html";i:1602840600;s:20:"./static/header.html";i:1602917348;s:28:"./static/js/projectdate.html";i:1602842102;}*/ ?>
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
			
			<div class="layui-col-xs4 layui-col-sm3 layui-col-md3 layui-col-lg3">
				<div class="layui-inline layui-input-inline">
					<label>等级范围：</label>
					<select id="sel_grade_min" lay-search="" class="layui-select" style="color: #aaa;">
						<option value="">最小</option>
						<option value="A" style="color: black;">A</option>
						<option value="B+" style="color: black;">B+</option>
						<option value="B" style="color: black;">B</option>
						<option value="C+" style="color: black;">C+</option>
						<option value="C" style="color: black;">C</option>
					</select>
					-
					<select id="sel_grade_max" lay-search="" class="layui-select" style="color: #aaa;">
						<option value="">最大</option>
						<option value="A" style="color: black;">A</option>
						<option value="B+" style="color: black;">B+</option>
						<option value="B" style="color: black;">B</option>
						<option value="C+" style="color: black;">C+</option>
						<option value="C" style="color: black;">C</option>
					</select>
				</div>
			</div>
			
			<div class="layui-col-xs4 layui-col-sm3 layui-col-md1 layui-col-lg1">
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
					<div class="layui-input-inline">
						<input type="text" class="layui-input" id="sel_time" placeholder="请选择日期范围">
					</div>
					<button class="layui-btn layui-btn-normal layui-btn-sm" id="lastday">前一天</button>
					<button class="layui-btn layui-btn-normal layui-btn-sm" id="nextday">后一天</button>
					
				</div>
			</div>


			<!-- <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
				<div class="layui-inline">
					<div class="layui-input-inline">
						<input type="text" class="layui-input" id="sel_date" name="sel_date" placeholder="yyyy-MM-dd">
					</div>
				</div>
			</div> -->

			<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
				<div class="layui-inline">
					<button class="layui-btn layui-btn-sm laymy-w1" id="search">搜索</button>
				</div>
			</div>
		</div>
		
	</div>

	<table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>
	<script type="text/html" id="head_toolbar">
		<?php if(\think\Session::get('type')!=6): ?>
		<div class="layui-btn-container">
          <button class="layui-btn layui-btn-sm" lay-event="insert">新增项目</button>
		</div>
		<?php endif; ?>
    </script>
	<script type="text/html" id="toolbar">
		<div class="layui-btn-container">
		  <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="view">查看</button>
		  <?php if(\think\Session::get('type')!=6): ?>
		  <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="ins_con">录入消费</button>
		  <button class="layui-btn layui-btn-sm" lay-event="update">编辑</button>
		  
		  <button class="layui-btn layui-btn-sm" lay-event="log" style="background-color:#5086B2">日志</button>

		  {{#  if(d.Status == 0){ }}
		  <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="del">启用</button>
		  {{#  } else { }}
		  <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="del">停用</button>
		  {{#  } }}
         
		  <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">删除</button>
		  <?php endif; ?>
		</div>
	</script>
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
        elem: '#sel_time',
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
        //var nowdate = document.getElementById("sel_time");
        $('#lastday').click(function () {
            //当前时间变量
            //var nowdate = document.getElementById("sel_time");
            var nowdate = $('#sel_time').val();
            if (nowdate.length < 10) {
                nowdate = new Date();
            } else {
                nowdate = new Date(nowdate.substr(0, 10));
            }
            console.log(nowdate);

            //新的时间
            var newdate = new Date(nowdate.setDate(nowdate.getDate() - 1));
            var str = formatDate(newdate, 'yyyy-MM-dd');
            $('#sel_time').val(str);
            return false;
        });
        $('#nextday').click(function () {
           // var nowdate = document.getElementById("sel_time");
           var nowdate = $('#sel_time').val();
            //console.log(nowdate);
            if (nowdate.length < 10) {
                nowdate = new Date();
            } else {
                nowdate = new Date(nowdate.substr(0, 10));
            }

            var newdate = new Date(nowdate.setDate(nowdate.getDate() + 1));
            var str = formatDate(newdate, 'yyyy-MM-dd');
            
            $('#sel_time').val(str);
            return false;
        })

    });
</script>
	<script>

		var table = layui.table, layer = layui.layer, $ = layui.$, laydate = layui.laydate;
		var myDate = new Date();
		var date = myDate.getDate();
		var h = myDate.getHours();       //获取当前小时数(0-23)
		var m = myDate.getMinutes();     //获取当前分钟数(0-59)
		var s = myDate.getSeconds();
		// 渲染日期
		// laydate.render({
		// 	elem: '#sel_date'
		// });
		var form_table = table.render({
			elem: '#formfields_table',
			height: "full-70",
			limit: 50,
			limits: [20, 50, 100, 200, 500],
			page: true,
			totalRow: true,//开启合计
			toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
			defaultToolbar: ['filter','exports', 'print'],
			url: '/public/index.php/get_project',
			method: 'get',
			cols: [[
				{ field: 'Id', title: 'ID', hide: true },
				<?php if(\think\Session::get('type') != 6): ?>
				{ field: 'Client', title: '客户', width:130, totalRowText: '合计' },
				<?php endif; ?>
				{ field: 'ProjectName', title: '项目名', width: 130 },
				{ field: 'ProjectGrade', title: '项目等级', width: 90 },
				<?php if(\think\Session::get('type')!=6): ?>
				{ field: 'Name', title: '负责人', width: 100 },
				<?php endif; ?>

				{ field: 'Address', title: '地域', width:"5%" },
				{ field: 'Brand', title: '品牌', width:"5%" },

				{ field: 'ExtensionStart', title: '开始时间', width:120, hide: true },
				{ field: 'ExtensionEnd', title: '结束时间', width:120,  hide: true },
				{ field: 'EstimatedCost', title: '目标成本', width: 110, sort: true },
				{ field: 'TargetNumber', title: '目标数量', width: 90},
				{ field: 'CustomerBudget', title: '预算', width: 80, sort: true },
				{ field: 'Remarks', title: '项目备注', width: 180 },
				{ field: 'Con', title: '昨日消费', width: 100, totalRow: true, sort: true },
				{ field: 'Res', title: '昨日资源', width: 100, totalRow: true, sort: true },
				{ field: 'Cos', title: '昨日成本', width: 100,totalRowText:'消费/资源', sort: true },
				{ field: 'Status', title: '状态', hide: true },
				// { field: 'logtime', title: '日志'},
				{ field: '', title: '操作', toolbar: '#toolbar', width: 380 }
			]],
			done: function (res, curr, count) {
				$.each(res.data, function (k, v) {
					var t1 = v.ExtensionStart
					var t2 = h + ':' + m + ':' + 's'
					var t3 = v.ExtensionEnd
					var flag = CompareDate(t1, t2, t3)
					if (flag) {
						$(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#CEECF5");
					}

					let tr = $(".layui-table-box tbody tr[data-index='" + k + "']");

					let but_view = tr.find('div.layui-btn-container button[lay-event="view"]')
					//标记Changes字段不为空的行
					if(v['Changes']!=null && v['Changes']!='' && v['Changes']!=undefined){
						// tr.css("background-color", "#649DF3");
						but_view.css("background-color", "#F78C2E");
						but_view.css("color","#eee");
                        but_view.addClass("layui-anim layui-anim-fadein layui-anim-loop");
					}

					//标记未确认状态的
					if(v.OK_Status){
						// console.log(v.OK_Status)
						//运营未确认
						if(v.OK_Status[0] == 0 && v.OK_Status[1] == 1){
							but_view.css("background-color","#ff0000");
							but_view.css("color","#eee");
							but_view.addClass("layui-anim layui-anim-fadein layui-anim-loop");
						}
						//维护未确认
						if(v.OK_Status[0] == 1 && v.OK_Status[1] == 0){
							but_view.css("background-color","#0000ff");
							but_view.css("color","#eee");
							but_view.addClass("layui-anim layui-anim-fadein layui-anim-loop");
						}
						//运营维护都未确认
						if(v.OK_Status[0] == 0 && v.OK_Status[1] == 0){
							but_view.css("background-color","rgb(0, 0, 255)");
							but_view.css("color","#eee");
							but_view.addClass("layui-anim layui-anim-fadein layui-anim-loop");
							
							//颜色交替变换
							setInterval(function(){
								if(but_view.css("background-color") == "rgb(0, 0, 255)"){
									but_view.css("background-color","rgb(255, 0, 0)");
								}
								if(but_view.css("background-color") == "rgb(255, 0, 0)"){
									but_view.css("background-color","rgb(0, 0, 255)");
								}
							},1000)
						}
					}

					let but = tr.find('div.layui-btn-container button[lay-event="ins_con"]')

					// 消费记录数 == 账号数
					if(v['ConCount']>0 && v['ConCount'] == v['ProCount']){
						but.attr('title','已录完，记录数：'+v['ConCount'])
						but.text('已录完')
					}

					//消费记录数 < 账号数
					if(v['ConCount']>0 && v['ConCount']<v['ProCount']){
						but.text('待录入')
						but.attr('title',v['ProCount']-v['ConCount']+'条待录入')
					}

					//已确定的换个颜色展示
					if(v['Entry']>0){
						but.text('已确认')
						but.attr('title','已确认')
						but.css("background-color", "#BAD136");
						// but.css("background-color", "#1E9FFF");
					}

					// //如果几天内未写入日志，则按钮闪烁提示
					// if(v['logtime']){
					// 	let tr = $(".layui-table-box tbody tr[data-index='" + k + "']");
					// 	let but = tr.find('div.layui-btn-container button[lay-event="log"]');
					// 	but.css("background-color", "#F78C2E");
					// 	but.css("border-color","#F4773A");
					// 	but.addClass("layui-anim layui-anim-fadein layui-anim-loop");
					// 	but.attr("title","超过"+v['logtime']+"天未写入日志");
					// }
				})


				//计算昨日成本
				var sumCon = $("[data-field='Con']:last").children().text();
				var sumRes = $("[data-field='Res']:last").children().text();
				var sumCos = sumCon/sumRes;
				$("[data-field='Cos']:last").children().text(sumCos.toFixed(2));

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
							title: "新增项目",
							area: ['50%', '700px'],
							content: '/public/index.php/ins_project'
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
				case 'view':
					{
						layer.open({
							type: 2,
							title: "查看项目详情",
							area: ['80%', 600],
							content: '/public/index.php/view_project?Id=' + obj.data.Id
						});
						break;
					}
				case 'ins_con':
					{
						layer.open({
							type: 2,
							title: "新增消费记录",
							offset:'100px',
							area: ['100%', 600],
							content: '/public/index.php/ins_promotion_con?Id=' + obj.data.Id,
							cancel: function(){ 
								//右上角关闭回调
								form_table.reload();
								//return false 开启该代码可禁止点击该按钮关闭
							}
						});
						break;
					}
				case 'del':
					{
						let status = data.Status ? '停用' : '启用'
						layer.confirm('确定' + status + '该项目吗', { icon: 3 }, function () {
							$.post('/public/index.php/del_project_do', { Id: data.Id }, function (data) {
								layer.msg(data.msg, function () {
									if (data.code === 0) {
										form_table.reload();
									}
								})
							}, 'json');
						})
						break;
					}
				case 'dels':
					{							
						layer.confirm('确定永久删除该项目？', { btn: ['确定', '取消'], icon: 3 }, function () {
							layer.confirm('再次提醒，删除项目不可恢复！！！', { btn: ['删除', '取消'], icon: 0, btnAlign: 'l' }, function () {
								$.post('/public/index.php/dels_project_do', { Id: data.Id }, function (data) {
									layer.msg(data.msg, function () {
										if (data.code === 0) {
											form_table.reload();
										}
									})
								}, 'json');
							});
						});
						break;
					}
				case 'update':
					{
						layer.open({
							type: 2,
							title: "编辑项目信息",
							area: ['80%', 600],
							content: '/public/index.php/upd_project?Id=' + obj.data.Id
						});
						break;
					}
				case 'log':
					{
						layer.open({
							type: 2,
							title: "编辑项目信息",
							area: ['80%', 600],
							shadeClose:true,
							content: '/public/index.php/log_project?Id=' + obj.data.Id
						})
						break;
					}
			}
		});
		//搜索
		$("#search").click(function () {
			var sel_client_id = $("#sel_client_id").val() || "";
			var sel_user_id = $('#sel_user_id').val() || "";
			var sel_status = $('#sel_status').val() || "";
			var sel_time = $('#sel_time').val() || "";
			// var sel_date = $('#sel_date').val() || "";
			var sel_grade_min = $('#sel_grade_min').val() || "";
			var sel_grade_max = $('#sel_grade_max').val() || "";
			form_table.reload({	//重载表格
				where: {
					sel_status: sel_status,
					sel_client_id: sel_client_id,
					sel_user_id: sel_user_id,
					sel_time:sel_time,
					// sel_date:sel_date,
					sel_grade_min:sel_grade_min,
					sel_grade_max:sel_grade_max
				}
			});
		});

		function CompareDate(t1, t2, t3) {
			var date = new Date();
			var a = t1.split(":");
			var b = t2.split(":");
			var c = t3.split(":");
			return (date.setHours(a[0], a[1]) < date.setHours(b[0], b[1]) && date.setHours(b[0], b[1]) < date.setHours(c[0], c[1]));
		}
	</script>
</body>