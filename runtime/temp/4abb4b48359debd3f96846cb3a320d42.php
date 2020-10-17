<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:67:"D:\phpstudy_pro\WWW\public/../application/push\view\push\index.html";i:1597728211;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
                    <input type="text"  class="layui-input" id="sel_phone" placeholder="请输入手机号码">
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
      <button class="layui-btn layui-btn-sm" lay-event="seturl">设置url</button>
    </div>
</script>
<?php endif; ?>

<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm layui-btn-info" lay-event="ok">确认提取</button>
        <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="dels">删除</button>
    </div>
</script>
<script>

    var table = layui.table,layer = layui.layer,$=layui.$,laypage=layui.laypage,laydate = layui.laydate;
    //日期范围
    laydate.render({
    elem: '#sel_time'
    , range: true,
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
            }
            //新的时间
            var newdate = new Date(nowdate.setDate(nowdate.getDate() - 1));
            var str = formatDate(newdate, 'yyyy-MM-dd');
            $('#sel_time').val(str + ' - ' + str);
        });
        $('#nextday').click(function () {
            var nowdate = $('#sel_time').val();
            if (nowdate.length < 10) {
                nowdate = new Date();
            } else {
                nowdate = new Date(nowdate.substr(13, 10));
            }
            var newdate = new Date(nowdate.setDate(nowdate.getDate() + 1));
            var str = formatDate(newdate, 'yyyy-MM-dd');
            $('#sel_time').val(str + ' - ' + str);
        })
    });
    var form_table = table.render({
        elem:'#formfields_table',
        height:"full-70",
        limit:20,
        limits:[20,50,100,200,500],
        page: true,
        totalRow: true,//开启合计
        toolbar: '#head_toolbar', //开启头部工具栏，并为其绑定左侧模板
        defaultToolbar:['exports','print'],
        url:'/push/1',
        method:'get',
        cols:[[
            {field:'Id',title:'ID',hide:true},
            {field:'Client',title:'客户',sort:true},
            {field:'Kw',title:'项目',sort:true},
            {field:'Phone',title:'电话',sort:true,width:120},
            {field:'Content',title:'推送内容',sort:true,width:'40%'},
            {field:'Cre_time',title:'时间',sort:true,width:'10%'},
            // {field:'Name',title:'推送人',sort:true},
            {field:'Ok_time',title:'提取时间',sort:true,width:"10%"},
            {field:'Being',title:'重复',width:60},
            
            {field:'Remarks',title:'备注',edit:true},
            {field:'',title:'操作',toolbar:'#toolbar',width:170}
        ]],
        done:function(res, curr, count){
            $.each(res.data,function(k,v){
                var status=v.Status
                var delete_time=v.delete_time
                var being = v.Being;
                if(being == '重复'){
                    $(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#88dd99");
                }
                if(status===1){
                    $(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#00B2EE");
                }
                if(delete_time){
                    $(".layui-table-box tbody tr[data-index='" + k + "']").css("background-color", "#ec8a8a");
                }
            })
        }
    });

    <?php if(\think\Session::get('type')<=4): ?>
    //头工具栏事件    
    table.on('toolbar(lay_formfields)', function(obj){
        switch (obj.event) {
            case 'seturl':
                layer.open({
                    type: 2,
                    title: "设置url",
                    shadeClose: true,//外部关闭
                    area: ['60%', '600px'],
                    content: '/push/push/seturl'
                });
                break;
        };
    });
    <?php endif; ?>


    //监听列工具行
    table.on('tool(lay_formfields)',function(obj){
        console.log(obj.data)
        var data = obj.data;
        var event = obj.event;
        switch (event) {
            case 'ok':
                {
                    $.ajax({
                        url: "/push/" + data.Id,
                        data: {},
                        type: "PUT",
                        dataType: "json",
                        success: function (data) {
                            layer.msg(data.msg, function () {
                                form_table.reload();
                            })
                        },
                        error: function (data) {
                            $.messager.alert('错误', data.msg);
                        }
                    });
                    break;
                }
            case 'dels':
                {
                    layer.confirm('是否确定删除？', { btn: ['确定', '取消'] }, function () {
                        $.ajax({
                            url: "/push/" + data.Id,
                            data: {},
                            type: "delete",
                            dataType: "json",
                            success: function (data) {
                                layer.msg(data.msg, function () {
                                    if (data.code === 0) {
                                        form_table.reload();
                                    }
                                })
                            },
                            error: function (data) {
                                $.messager.alert('错误', data.msg);
                            }
                        });
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
                url:"/update_push_remark/",
                data:{'Id':data.Id,'Remarks':data.Remarks},
                type:"post",
                dataType:"json",
                success:function(data){
                    layer.msg('操作成功，备注更改为：'+ value,function(){
                        if(data.code === 0)
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
        var sel_time= $('#sel_time').val()||"";
        var sel_phone= $('#sel_phone').val()||"";
        form_table.reload({	//重载表格
            where:{
                sel_client_id:sel_client_id,
                sel_time:sel_time,
                sel_phone:sel_phone,
            }
        });
    });

</script>
</body>