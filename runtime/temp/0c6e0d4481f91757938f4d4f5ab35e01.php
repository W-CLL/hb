<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:89:"D:\phpstudy_pro\WWW\public/../application/promotion\view\promotion_rec\promotion_rec.html";i:1602833119;s:20:"./static/header.html";i:1602917348;s:27:"./static/js/selectdate.html";i:1602901063;}*/ ?>
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

				<div class="layui-col-xs4 layui-col-sm3 layui-col-md4 layui-col-lg4">
					<div class="layui-inline">
						<div class="layui-input-inline">
							<input type="text" class="layui-input" id="sel_time" placeholder="请选择日期范围">
						</div>
						<button class="layui-btn layui-btn-normal layui-btn-sm" id="lastday">前一天</button>
						<div class="layui-input-inline">
							<button class="layui-btn layui-btn-normal layui-btn-sm" id="nextday">后一天</button>
						</div>
					</div>
				</div>


				
				<div class="layui-col-xs4 layui-col-sm3 layui-col-md1 layui-col-lg1">
					<div class="layui-inline">
						<div class="layui-input-inline">
							<select class="layui-input" name="sel_status" id="sel_status">
								<option value="">全部</option>
								<option value="0">未打款&nbsp;&nbsp;</option>
								<option value="1">已打款&nbsp;&nbsp;</option>
								<option value="2">已到账&nbsp;&nbsp;</option>
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
		<script type="text/html" id="head_toolbar">
	         </script>
		<table id="formfields_table" lay-filter="lay_formfields" class="layui-table">
			<!-- recStatus模板标签名称用来修改 -->
			<script type="text/html" id="recStatus">
					{{#  if(d.Status == '未打款'){ }}
						<span style="color:red">{{d.Status}}</span>
					{{#  } else if(d.Status == '已打款'){ }}
						<span style="color:blue">{{d.Status}}</span>
					{{#  } else { }}
						<span>{{d.Status}}</span>
					{{#  } }}
			</script>
		</table>
		<script type="text/html" id="toolbar">
			<div class="layui-btn-container">
				<button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="detail">查看</button>
				<?php if(\think\Session::get('type')<3): ?>
				<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="havepay">已打款</button>
				<?php endif; ?>
				
				<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="havebill">已到账</button>
				
			<button class="layui-btn layui-btn-sm" lay-event="update">编辑</button>
			<?php if(\think\Session::get('auth')<3): ?>
			<button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">删除</button>
			<?php endif; ?>
			</div>
		</script>

		<script type="text/html" id="cz">
			<div class="layui-table-cell ">充值</div>
		</script>
		<script type="text/html" id="rm">
			<div class="layui-table-cell "> 广告币,人民币</div>
		</script>

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
		var table = layui.table,layer = layui.layer,$=layui.$,laypage=layui.laypage;
		var form_table = table.render({
			elem:'#formfields_table',
			height:"full-70",
			limit:20,
            limits:[20,50,100,200,500],
			page: true,
			totalRow: true,
			toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
            defaultToolbar:['exports','print'],
			url:'/public/index.php/get_promotion_rec',
			method:'get',
			cols:[[
				{field:'Id',title:'ID',hide:true},
				{field:'Remarks',title:'账号备注',totalRowText: '合计',sort:true,width:"10%"},
				{field:'Pro_User',title:'推广账号',sort:true,width:"10%"},

				{field:'',title:'备注',toolbar:'#cz',width:"8%"},

				{field:'Rec_B',title:'充值广告币',totalRow: true,sort:true,width:"8%"},

				{field:'',title:'R备注',toolbar:'#rm',width:"8%"},

				{field:'Money_Rec',title:'充值人民币',totalRow: true,sort:true,width:"8%"},
				{field:'Cre_time',title:'充值时间',sort:true,width:"12%"},
                {field:'SumMon',title:'账号总余额',sort:true,width:'8%'},
				{field:'SumCon7',title:'近7天日均消费',sort:true,width:'8%'},


				
				{field:'RecRemarks',title:'充值备注',sort:true,width:"8%"},
				{field:'Name',title:'最后录入人',sort:true,width:"8%"},
				{field:'Status',title:'充值状态',width:"90",templet:'#recStatus'},
                {field:'',title:'操作',toolbar:'#toolbar',width:350}
			]],
			done:function(res, curr, count){
			}
		});
		//将上述表格示例导出为 csv 文件
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
				case 'view':
				{
						layer.open({
							  type: 2,
				              title:"查看项目详情",
				              area:['80%',600],
				              shadeClose:true,//外部关闭
							  content: '/public/index.php/view_project?Id='+obj.data.Id
							});
						break;
				}
				case 'detail':
					{
						layer.open({
							type: 2,
							title:"充值记录详情",
							shadeClose:true,//外部关闭
							area:['80%',600],
							content: '/public/index.php/detail_promotion_rec?Id='+data.Id
							});
							break;
					}
				<?php if(\think\Session::get('auth')<3): ?>
				case 'dels':
				{
					layer.confirm('是否确定删除？',{btn:['确定','取消']},function(){
						$.post('/public/index.php/dels_promotion_rec_do',{Id:data.Id},function(data){
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
				<?php endif; ?>
				case 'update':
				{
					layer.open({
						  type: 2,
			              title:"编辑充值记录",
			              shadeClose:true,//外部关闭
			              area:['80%',600],
						  content: '/public/index.php/upd_promotion_rec?Id='+obj.data.Id
						});
					break;
				}
				<?php if(\think\Session::get('type')<3): ?>
				case 'havepay':
				{
					layer.confirm('是否确定？',{btn:['确定','取消'],title:'确认打款',icon:3},function(){
						$.post(
							'/public/index.php/havepay_promotion_rec_do',
							{Id:data.Id},
							function(data){
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
				<?php endif; ?>
				case 'havebill':
				{
					layer.confirm('是否确定？',{btn:['确定','取消'],title:'确认到账',icon:3},function(){
						$.post(
							'/public/index.php/havebill_promotion_rec_do',
							{Id:data.Id},
							function(data){
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
			}
		});
        //搜索
		$("#search").click(function(){
			var sel_pro_user = $("#sel_pro_user").val()||"";
			var sel_remarks= $('#sel_remarks').val()||"";
			var sel_time= $('#sel_time').val()||"";
			var sel_status = $('#sel_status').val()||"";
			form_table.reload({	//重载表格
				where:{
					sel_pro_user:sel_pro_user,
					sel_remarks:sel_remarks,
					sel_time:sel_time,
					sel_status:sel_status,
				}
			});
		});
	 </script>
	 </body>