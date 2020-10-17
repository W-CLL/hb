<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:93:"D:\phpstudy_pro\WWW\public/../application/promotion\view\promotion_con\cli_promotion_con.html";i:1602726890;s:20:"./static/header.html";i:1602917348;s:27:"./static/js/selectdate.html";i:1602901063;}*/ ?>
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
		<div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
			<div class="layui-inline">
				<div class="layui-input-inline">
					<select id="sel_project_name" lay-verify="required" lay-search="" class="layui-select">
						<option value="">请选择项目</option>
						<?php foreach($pro as $k=>$v): ?>
						<option value="<?php echo $v['ProjectName']; ?>"><?php echo $v['ProjectName']; ?></option>
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
<?php if(\think\Session::get('type')<=4): ?>
<script type="text/html" id="head_toolbar">
  <div class="layui-btn-container">
    <button class="layui-btn layui-btn-sm" lay-event="push">推送到客户微信</button>
  </div>
</script>
<script type="text/html" id="toolbar">
	<div class="layui-btn-container">
	  <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="push">推送消息</button>
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

	var table = layui.table,layer = layui.layer,$=layui.$,laypage=layui.laypage;
	var resTotal = {}
	var form_table = table.render({
	elem:'#formfields_table',
	height:"full-70",
	limit:20,
	limits:[20,50,100,200,500],
	page: true,
	totalRow: true,//开启合计
	toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
	defaultToolbar:['exports','print'],
	url:'/public/index.php/get_cli_promotion_con',
	method:'get',
	cols:[[
	    
	    <?php if(\think\Session::get('type')<=4): ?>
	    {checkbox:'true'},
        <?php endif; ?>
        
		{field:'Id',title:'ID',hide:true},
        {field:'Client_Id',title:'客户Id',hide:true},
		<?php if(\think\Session::get('type')!=5): ?>{field:'Client',title:'客户',sort:true},<?php endif; ?>
		{field:'ProjectName',title:'项目',totalRowText: '合计',sort:true},
		{field:'Money_Coin',title:'消费币',sort:true,totalRow: true, width:100},
		{field:'Money_Con',title:'消费/元',sort:true,totalRow: true, width:105},
		
		{field:'ShowCon',title:'展现',sort:true,totalRow: true},
			
		{field:'Click',title:'点击',sort:true,totalRow: true},
		{field:'Dialogue',title:'对话数量',sort:true,totalRow: true, width:105},
		{field:'Phone',title:'留电数量',sort:true,totalRow: true,  width:105},
		{field:'Message',title:'留言数量',sort:true,totalRow: true, width:105},
		{field:'CueSum',title:'线索总数',totalRow: true,sort:true, width:105},
		{field:'DialogueCost',title:'对话成本',sort:true, width:105},
		{field:'CueCost',title:'线索成本',sort:true, width:105},
		{field:'Sum',title:'余额',sort:true},
		{field:'Date',title:'时间',sort:true},
		
		<?php if(\think\Session::get('type')<=2): ?>
	    { field: '', title: '操作', toolbar: '#toolbar' }
        <?php endif; ?>
	]],
	done:function(res, curr, count){
		// console.log(res);
		// 用服务器返回的合计数据替换layui.table的合计文本
		// $("[data-field='ProjectName']:last").children().text('合计');
		if(res.count>=1){
			$("[data-field='Money_Coin']:last").children().text(res.totalRow.ShowCoin);
			// $("[data-field='Money_Con']:last").children().text(res.totalRow.Money_Con);
			$("[data-field='ShowCon']:last").children().text(res.totalRow.ShowCon);
			$("[data-field='Click']:last").children().text(res.totalRow.Click);
			$("[data-field='Dialogue']:last").children().text(res.totalRow.Dialogue);
			$("[data-field='Phone']:last").children().text(res.totalRow.Phone);
			$("[data-field='Message']:last").children().text(res.totalRow.Message);
			$("[data-field='CueSum']:last").children().text(res.totalRow.CueSum);
			$("[data-field='DialogueCost']:last").children().text(res.totalRow.DialogueCost);
			$("[data-field='CueCost']:last").children().text(res.totalRow.CueCost);
		}
	}
	});

    //头工具栏事件
    table.on('toolbar(lay_formfields)', function(obj){
        var checkStatus = table.checkStatus(obj.config.id);
        switch(obj.event){
            case 'push':
                var data = checkStatus.data;
                if(data.length<1){
                  layer.alert('未选中数据');
                  return ;
                }
                // let json = JSON.stringify(data);
                layer.confirm('确认推送？',function(){
                     $.post('/public/index.php/wxapi/gzh/push_speed/',{data:data},function(res){
				        if(res.code == 0){
				            layer.msg('推送成功',{time:2000})
				        }else{
				            layer.open({title:'推送失败',content:res.msg});
				        }
				    },'json')
                })
                break;
        };
    });

	//监听列工具行
	table.on('tool(lay_formfields)', function (obj) {
		var data = obj.data;
		var event = obj.event;
		var tr = obj.tr;
		switch (event) {
			case 'push':
				{
				    $.post('/public/index.php/wxapi/gzh/push_speed/',{data:data},function(res){
				        if(res.code == 0){
				            layer.msg('推送成功',{time:2000})
				        }else{
				            layer.open({title:'推送失败',content:res.msg});
				        }
				    })
					break;
				}
		}
	});
	
	//搜索
	$("#search").click(function(){
		var sel_client_id = $("#sel_client_id").val()||"";
		var sel_project_name= $('#sel_project_name').val()||"";
		var sel_time= $('#sel_time').val()||"";
		form_table.reload({	//重载表格
			where:{
				sel_client_id:sel_client_id,
				sel_project_name:sel_project_name,
				sel_time:sel_time,
			}
		});
	});

</script>
</body>