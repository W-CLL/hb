<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:93:"D:\phpstudy_pro\WWW\public/../application/promotion\view\promotion_con\ins_promotion_con.html";i:1602726935;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
<table id="formfields_table" lay-filter="lay_formfields" class="layui-table"></table>
<script type="text/html" id="toolbar">
    {{#  if(d.Id){ }}
        <button class="layui-btn layui-btn-sm layui-btn-disabled" title="禁止重复录入">确认</button>
    {{#  } else { }}
        <button class="layui-btn layui-btn-sm" lay-event="add">确认</button>
    {{#  } }}
</script>
<script type="text/html" id="titleTpl">
    <a href="/detail/{{d.id}}" class="layui-table-link">{{d.title}}</a>
</script>
</div>
</div>
<?php if(\think\Session::get('type')<=2): ?>
<table id="table2" lay-filter="lay_table2" class="layui-table"></table>
<table id="table3" lay-filter="lay_table3" class="layui-table"></table>
<div style="text-align: right;">
    <button id="submit_table3_data2" type="button" class="layui-btn layui-btn-normal">确定不展示</button>
    <button id="submit_table3_data" type="button" class="layui-btn layui-btn-normal">确定并展示</button>
</div>
<?php endif; ?>
<script>
    var table = layui.table,layer = layui.layer,$=layui.$,laydate = layui.laydate;
    //数据表格的初始数据
    var cols2 = [[
            { field: 'Title', title: '', width: 150 },
            { field: 'Spend', title: '总消费', edit: 'text' },
            { field: 'Clues', title: '总线索', edit: 'text' },
            { field: 'Cost', title: '总成本' },
            { field: 'Pro', title: '消费差(外-内)', width:120 }
        ]],
        data2 = [
            { 'Title': '内部', 'Spend': 0, 'Clues': 0, 'Cost': 0 },
            { 'Title': '外部', 'Spend': 0, 'Clues': 0, 'Cost': 0 ,'pro_status':false}
        ],
        data3 = [];
    var form_table = table.render({
        elem:'#formfields_table',
        // height:"250px",
        url:"/public/index.php/get_ins_promotion_con?Id=<?php echo $Id; ?>",
        method:'get',
        cols:[[
            {field:'Client_Id',title:'ID',hide:true},
            {field:'Pro_Id',title:'Pro_Id',hide:true},
            
            {field:'kf53_Id',title:'kf53_Id',hide:true},
            {field:'Project_Id',title:'Project_Id',hide:true},
            {field:'Name',title:'客户',sort:true},
            {field:'ProjectName',title:'项目名',sort:true, width: 80 },
            {field:'Pro_User',title:'推广账号',sort:true,width:150},
            
            {field:'Pro_Psw',title:'密码', width: 80 },
            {field:'User_53',title:'53账号'},
            {field:'Psw_53',title:'53密码', width: 80 },
            {field:'Rebate',title:'平台返点', width: 90 },
            <?php if(\think\Session::get('type')<=2): ?>
            {field:'Client_Rebate',title:'客户返点', width: 90 },
            <?php endif; ?>
            
            {field:'Remarks',title:'推广账号备注'},
            {field:'Con_B',title:'消费/币',edit: 'text'},
            
            {field:'ShowCon',title:'展现',edit:'text'},
            {field:'Click',title:'点击',edit:'text'},
            {field:'Dialogue',title:'对话',edit: 'text'},
            {field:'Phone',title:'留电',edit: 'text'},
            {field:'Message',title:'留言',edit: 'text'},
            {field:'Cost',title:'成本',width:80},
            {field:'Date',title:'日期',width:102},
            {field:'ConRemarks',title:'消费备注',edit: 'text'},
            {field:'',title:'操作',toolbar:'#toolbar',width:80}
        ]],
        done:function(res, curr, count){
            // console.log(res);
            var pro = $('td[data-field="Pro_Psw"]')
            pro.dblclick(function(e){
                // console.log($(e.target).parent().siblings('td[data-field="Pro_Id"]').text());
                var psw = $(e.target)
                var Pro_Id = psw.parent().siblings('td[data-field="Pro_Id"]').text()
                $.post('/public/index.php/get_pro_key',{Pro_Id:Pro_Id},function(res){
                    if(res.code == 0){psw.text(res.key)}
                },'json')
            })
            var pro = $('td[data-field="Psw_53"]')
            pro.dblclick(function(e){
                var kf53_key = $(e.target)
                var client_53_id= kf53_key.parent().siblings('td[data-field="kf53_Id"]').text()
                $.post('/public/index.php/get_kf53_key',{client_53_id:client_53_id},function(res){
                    if(res.code == 0){kf53_key.text(res.key)}
                },'json')
            })
            
            <?php if(\think\Session::get('type')<=2): ?>
            var SpendSum = 0, Clues = 0, SPendSum2 = 0, pro_status = false;
            res.data.forEach(element => {
                SpendSum += Number(element.Money_Con)
                Clues += Number(element.Phone) + Number(element.Message)
                SPendSum2 += Number(element.Cli_Money_Con)
                if (Math.round(element.Cli_Money_Coi) || Math.round(element.Cli_Money_Con)) {
                    pro_status = true;//外部消费录入状态
                }
                // element.Cost = element.Con_B/element.Client_Rebate/(element.Phone+element.Message)
            });
            //为表格初始化赋值
            data2 = [
                { 'Title': '内部', 'Spend': Math.round(SpendSum*100)/100, 'Clues': Clues, 'Cost': Math.round((SpendSum / Clues)*100)/100 },
                { 'Title': '外部', 'Spend': Math.round(SPendSum2*100)/100, 'Clues': Clues, 'Cost': Math.round((SPendSum2/Clues)*100)/100 ,'pro_status':pro_status}
            ];
            //为标题赋值
            cols2[0][0].title = res.data[0].ProjectName
            form_table2.reload({
                data: data2
            });
            //表3为表格1 的数据
            data3 = res.data;
            form_table3.reload({
                data: data3
            })
            <?php endif; ?>

        }
    })
    //监听行工具事件
    table.on('tool(lay_formfields)', function(obj){
        var data = obj.data;
        delete data.Psw_53
        delete data.Pro_Psw
        if(obj.event === 'add'){
            $.post('/public/index.php/ins_promotion_con_do',{data:data},function(obj){
                layer.msg(obj.msg,function(index){
                    /*if(obj.code===0){
                        window.parent.location.reload();//刷新父页面
                    }*/
                });
            },'json');
            obj.del()
        }
    });
    <?php if(\think\Session::get('type')<=2): ?>
    var Spend=[],Clues=[];//总花费和总线索
    //监听单元格编辑
    table.on('edit(lay_formfields)', function(obj){
        // console.log(obj)
        var value = obj.value //得到修改后的值
        ,data = obj.data //得到所在行所有键值
        ,field = obj.field; //得到字段
        // 平台返点data.Rebate 客户返点data.Client_Rebate
        if(field == 'Con_B'){ //消费币被编辑
            Spend[data.Pro_Id] = Math.round((Number(value)/data.Rebate)*100)/100//单条记录的内部消费
        }else if(field == 'Phone' || field == 'Message') { //留电，留言被编辑
            data.Phone = data.Phone || 0
            data.Message = data.Message || 0
            Clues[data.Pro_Id] = Number(data.Phone) + Number(data.Message)
        }
        var SpendSum=0,CluesSum=0
        Spend.forEach(i => {
            SpendSum += i
        });
        Clues.forEach(i => {
            CluesSum += i
        })
        data2 = [
            { 'Title': '内部', 'Spend': SpendSum, 'Clues': CluesSum, 'Cost': Math.round((SpendSum / CluesSum)*100)/100 },
            { 'Title': '外部', 'Spend': data2[1].Spend, 'Clues': CluesSum, 'Cost': Math.round((data2[1].Spend / CluesSum)*100)/100 }
        ];
        form_table2.reload({
            data: data2
        })
        
        data3.forEach((element, index) => {
            if (element.Pro_Id == data.Pro_Id) {
                //内部消费
                data3[index].Money_Con = Spend[data.Pro_Id]
                // 外部消费 =（单条记录的内部消费/总消费)*外部消费总数
                data3[index].Cli_Money_Con = Math.round(((Spend[data.Pro_Id] / SpendSum) * data2[1].Spend)*100)/100
                // 外部消费币 = 外部消费*客户返点
                data3[index].Cli_Money_Coin = data3[index].Cli_Money_Con * data.Client_Rebate
            }
        });
        form_table3.reload({
            data:data3
        })
    });

    // 第二个表格
    var form_table2 = table.render({
        elem: '#table2',
        // height: "250px",
        width:600,
        cols: cols2,
        done: function (res, curr, count) {
            var data = res.data
            var Pro = $('[lay-id="table2"] tbody tr[data-index="1"] td[data-field="Pro"]').children().text(data[1].Spend-data[0].Spend)
            // console.log(data[1].pro_status)
            if(!data[1].pro_status){
                $('[lay-id="table2"] tbody tr[data-index="1"] td[data-field="Spend"]').css('background-color','#F8DDC3')
            }
        }
        , data: data2
    })
    //监听单元格编辑
    table.on('edit(lay_table2)', function (obj) {
        var value = obj.value //得到修改后的值
            , data = obj.data //得到所在行所有键值
            , field = obj.field; //得到字段
        if (data.Title == '外部') {
            if(field == 'Spend'){
                data2[1].Spend = value;
            }else if(field == 'Clues'){
                data2[1].Clues = value;
            }
            data2[1].Cost = Math.round((data2[1].Spend/data2[1].Clues)*100)/100
            form_table2.reload({data:data2})
            data3.forEach((element, index) => {
                //内部消费
                // data3[index].Money_Con = Spend[data.Pro_Id]
                // 外部消费 =（单条记录的内部消费/内部总消费)*外部消费总数
                data3[index].Cli_Money_Con = Math.round(((data3[index].Money_Con / data2[0].Spend) * data2[1].Spend)*100)/100
                // 外部消费币 = 外部消费*客户返点
                data3[index].Cli_Money_Coin = Math.round((data3[index].Cli_Money_Con * data3[index].Client_Rebate)*100)/100
            });
        }else if(data.Title == '内部'){
            if(field == 'Spend'){
                data2[0].Spend = value;
            }else if(field == 'Clues'){
                data2[0].Clues = value;
            }
            data2[0].Cost = Math.round((data2[0].Spend/data2[0].Clues)*100)/100
            form_table2.reload({data:data2})
        }
        form_table3.reload({
            data: data3
        })
    });

    // 第三个表格
    var form_table3 = table.render({
        elem: '#table3',
        // height: "250px",
        // width: 500,
        totalRow: true,
        
        data: data3, 
        cols: [[
            { field: '', title: '', width: 80, totalRowText: '合计'},
            { field: 'Client_Id', title: 'ID', hide: true ,totalRow: true},
            { field: 'Pro_Id', title: 'Pro_Id', hide: true ,totalRow: true},
            { field: 'Project_Id', title: 'Project_Id', hide: true ,totalRow: true},
            { field: 'Con_B', title: '消费/币', width: 80,totalRow: true },
            { field: 'ShowCon', title: '展现', width: 80 ,totalRow: true },
            { field: 'Click', title: '点击' , width: 80  ,totalRow: true},
            { field: 'Dialogue', title: '对话' , width: 80,totalRow: true },
            { field: 'Phone', title: '留电' , width: 80,totalRow: true },
            { field: 'Message', title: '留言', width: 80  ,totalRow: true},
            { field: 'Date', title: '日期' , width: 110},
            { field: 'ConRemarks', title: '消费备注', width: 100},
            { field: 'Money_Con', title: '内部消费' ,totalRow: true},
            { field: 'Cli_Money_Con', title: '外部消费' ,totalRow: true},
            { field: 'Cli_Money_Coin', title: '外部消费币' ,totalRow: true},
        ]],
        done: function (res, curr, count) {
            //取整数
            var total_Con_B = $('[lay-id="table3"] .layui-table-total [data-field="Con_B"]').children()
            var total_ShowCon = $('[lay-id="table3"] .layui-table-total [data-field="ShowCon"]').children()
            var total_Click = $('[lay-id="table3"] .layui-table-total [data-field="Click"]').children()
            var total_Dialogue = $('[lay-id="table3"] .layui-table-total [data-field="Dialogue"]').children()
            var total_Phone = $('[lay-id="table3"] .layui-table-total [data-field="Phone"]').children()
            var total_Message = $('[lay-id="table3"] .layui-table-total [data-field="Message"]').children()
            total_Con_B.text(Math.round(total_Con_B.text()))
            total_ShowCon.text(Math.round(total_ShowCon.text()))
            total_Click.text(Math.round(total_Click.text()))
            total_Dialogue.text(Math.round(total_Dialogue.text()))
            total_Phone.text(Math.round(total_Phone.text()))
            total_Message.text(Math.round(total_Message.text()))
        }
    })

    $('#submit_table3_data').click(function(){
        // console.log(data3)
        layer.confirm('确认提交?', {icon: 3, title:'提示'}, function(index){
            layer.close(index);
            $.post('/public/index.php/upd_promotion_con_all_do?status=1',{data:data3},function(res){
                layer.alert(res.msg)
                // form_table.reload()
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layui.table.reload("formfields_table");//刷新父页面的表格
                parent.layer.close(index); //再执行关闭
            })
        });
        return false;
    })
    
    $('#submit_table3_data2').click(function(){
        // console.log(data3)
        layer.confirm('确认提交?', {icon: 3, title:'提示'}, function(index){
             layer.close(index);
            $.post('/public/index.php/upd_promotion_con_all_do/?status=0',{data:data3},function(res){
                layer.alert(res.msg)
                // form_table.reload()
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layui.table.reload("formfields_table");//刷新父页面的表格
                parent.layer.close(index); //再执行关闭
            })
        });
        return false;
    })
    <?php endif; ?>
</script>
</body>