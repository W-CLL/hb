<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:78:"D:\phpstudy_pro\WWW\public/../application/customer\view\customer\customer.html";i:1602926853;s:20:"./static/header.html";i:1602917348;s:27:"./static/js/selectdate.html";i:1602901063;}*/ ?>
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
  <div class="layui-fluid">
    <!--layui-fluid -->
    <div class="layui-row">
      
      <div class="layui-col-xs4 layui-col-sm3 layui-col-md3 layui-col-lg3">
        <h1><?php echo \think\Session::get('type'); ?></h1>
        <?php if((\think\Session::get('type')!='6') OR (\think\Session::get('type')!='5')): ?>
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
    <?php endif; ?>

        <div class="layui-inline">
          <div class="layui-input-inline">
            <select id="sel_join" class="layui-select">
              <option value="">请选择是否重复</option>
              <option value="repeat">重复</option>
              <option value="notrepeat">不重复</option>
            </select>
          </div>
        </div>
      </div>
      

      <div class="layui-col-xs4 layui-col-sm3 layui-col-md3 layui-col-lg3">
        <div class="layui-inline">
          <div class="layui-input-inline">
            <input
              type="text"
              class="layui-input"
              id="sel_time"
              placeholder="请选择日期范围"
            />
          </div>
          <button class="layui-btn layui-btn-normal layui-btn-sm" id="lastday">
            前一天
          </button>
          <div class="layui-input-inline">
            <button
              class="layui-btn layui-btn-normal layui-btn-sm"
              id="nextday"
            >
              后一天
            </button>
          </div>
        </div>
      </div>
      <div class="layui-col-xs4 layui-col-sm3 layui-col-md4 layui-col-lg4">
        <div class="layui-inline">
          <div class="layui-input-inline">
            <input
              type="text"
              class="layui-input"
              id="sel_url"
              placeholder="请输入Url关键字"
            />
          </div>
        </div>
        <div class="layui-inline">
          <div class="layui-input-inline">
            <input
              type="text"
              class="layui-input"
              id="sel_phone"
              placeholder="请输入手机号码"
            />
          </div>
        </div>
      </div>
      <!-- <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
            </div> -->
      <div class="layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg2">
        <div class="layui-inline">
          <button class="layui-btn layui-btn-sm laymy-w1" id="search">
            搜索
          </button>
        </div>
      </div>
    </div>
  </div>

  <table
    id="formfields_table"
    lay-filter="lay_formfields"
    class="layui-table"
  ></table>
  <?php if(\think\Session::get('auth')<3): ?>
  <script type="text/html" id="toolbarDemo">
    <div class="layui-btn-container">
      <button class="layui-btn layui-btn-sm" lay-event="delData">
        删除选中行数据
      </button>
    </div>
  </script>
  <?php endif; ?>
  <script type="text/html" id="toolbar">
    <div class="layui-btn-container">
      <?php if(\think\Session::get('auth')<3||\think\Session::get('type')==5): ?>
      <button class="layui-btn layui-btn-sm layui-btn-info" lay-event="ok">
        确认查收</button
      ><?php endif; ?>
      <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">
        删除
      </button>
    </div>
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
    var table = layui.table,layer = layui.layer,$=layui.$;

    var form_table = table.render({
    	elem:'#formfields_table',
    	limit:20,
              limits:[20,50,100,200,500],
    	page: true,
              defaultToolbar:['exports','print'],
    	url:'/public/index.php/get_customer',
              toolbar: '#toolbarDemo', //开启头部工具栏，并为其绑定左侧模板
    	method:'get',
              // width:1920,
    	cols:[[
                  <?php if(\think\Session::get('auth')<3): ?> {type:'checkbox'},<?php endif; ?>
    		{field:'Id',title:'ID',hide:true},
                  <?php if(\think\Session::get('type') != 6): ?>
                  {field:'Client',title:'所属客户'},
                  <?php endif; ?>
                  {field:'ProgramName',title:'项目'},
    		{field:'Sub_Time',title:'时间',width:160},
    		{field:'Name',title:'姓名或城市'},
    		{field:'Phone',title:'手机号码',width:120},
    		{field:'Kw',title:'搜索词'},
    		{field:'Search',title:'搜索来源'},
    		{field:'UrlKw',title:'关键词'},
    		{field:'Content',title:'留言内容'},
                  {field:'Ip',title:'Ip'},
                  {field:'Url',title:'访问url',width:400},
    		{field:'Status',title:'状态',hide:true},
    		{field:'Being',title:'是否重复',width:60},
    		{field:'Ok_time',title:'提取时间'},
                  {field:'Remarks',title:'备注',edit:'text'},
    		{field:'',title:'操作',toolbar:'#toolbar',width:170}
    	]],
    	done:function(res, curr, count){
    		$.each(res.data,function(k,v){
    			var status=v.Status
    			var moblie=v.Moblie
                      var ok=v.Ok
                      if(moblie){
                          $(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#B4EEB4");
                      }
    			if(status===0){
    			$(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#ec8a8a");
    			}
                      if(ok===1){
                          $(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#ec8a8a");
                      }
    		})
    	},

    });
    //监听列工具行
    table.on('tool(lay_formfields)',function(obj){
    	var data = obj.data;
    	var event = obj.event;
    	var tr = obj.tr;
    	switch(event)
    	{
    		case 'dels':
    		{
    			layer.confirm('是否确定删除？',{btn:['确定','取消']},function(){
    				$.post('/public/index.php/dels_customer_do',{Id:data.Id},function(data){
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
    		case 'ok':
    		{
    				$.post('/public/index.php/ok_customer_do',{Id:data.Id},function(data){
    					layer.msg(data.msg,function(){
    						if(data.code === 0)
    						{
    							form_table.reload();
    						}
    					})
    				},'json');
    			break;
    		}
    	}
    });
          //监听头部工具行
          table.on('toolbar(lay_formfields)',function(obj){
              var data = table.checkStatus('formfields_table');
              var event = obj.event;
              var tr = obj.tr;
              switch(event)
              {
                  case 'delData':
                  {
                      layer.confirm('是否确定删除？',{btn:['确定','取消']},function(){
                          $.post('/public/index.php/dels_customer_do',{data:data},function(data){
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
          //监听单元格编辑
          table.on('edit(lay_formfields)', function(obj){
                  // console.log(obj.data)
                  var value = obj.value //得到修改后的值
                  ,data = obj.data //得到所在行所有键值
                  ,field = obj.field; //得到字段
                  layer.confirm('是否确定修改备注？',{btn:['确定','取消']},function(){
                      $.ajax({
                          url:"/public/index.php/upd_customer_remark",
                          data:{'Id':data.Id,'Remarks':data.Remarks},
                          type:"post",
                          dataType:"json",
                          success:function(data){
                              layer.msg('操作成功，备注更改为：'+ value,function(){
                                  if(data.code === 0){}
                                  {form_table.reload();}
                              })
                          },
                          error:function(data){
                              $.messager.alert('错误',data.msg);
                          }
                      });
                  });
              });

          //搜索
    $("#search").click(function(){
    	var sel_client_id = $("#sel_client_id").val()||"";
    	var sel_url = $("#sel_url").val()||"";
    	var sel_time = $('#sel_time').val()||"";
    	var sel_phone = $('#sel_phone').val()||"";
    	var sel_join = $('#sel_join').val()||"";
    	form_table.reload({	//重载表格
    		where:{
                      sel_time:sel_time,
    			sel_client_id:sel_client_id,
                      sel_url:sel_url,
                      sel_phone:sel_phone,
                      sel_join:sel_join,
    		}
    	});
    });
  </script>
</body>
