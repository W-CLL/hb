<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:63:"D:\phpstudy_pro\WWW\public/../application/kw\view\kw\input.html";i:1597728223;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
    		 <div class="layui-col-xs-offset4 layui-col-sm-offset4 layui-col-md-offset4 layui-col-lg-offset4" style="margin-bottom: 30px;">
    		  <h2>对话转换项目</h2>
    		 </div >

		<form class="layui-form" action="" >
            <div class="layui-form-item">
                <label class="layui-form-label">所属客户:</label>
                <div class="layui-input-block">
                    <input type="text" id="client" required  lay-verify="required" placeholder="这里显示客户名" autocomplete="off" class="layui-input">
                    <input type="hidden" name="client_id" id="client_id">
                </div>
            </div>
  <div class="layui-form-item">
    <label class="layui-form-label">项目名:</label>
    <div class="layui-input-block">
      <input type="text" id="title" name="kw" required  lay-verify="required" placeholder="这里显示项目名" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item layui-form-text">
    <label class="layui-form-label">文本域</label>
    <div class="layui-input-block">
      <textarea name="content" placeholder="请粘贴要放入的对话内容" class="layui-textarea" data></textarea>
    </div>
  </div>
            <div class="layui-form-item show-push">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="add">立即发送</button>
                </div>
            </div>
</form>
<script>
    var table = layui.table,layer = layui.layer,$=layui.$,laydate = layui.laydate,form = layui.form;
    //监听提交
    form.on('submit(add)',function(data){
        $.post('/push',data.field,function(obj){
            layer.msg(obj.msg,function(){
                if (obj.code==0){
                    window.location.reload();//刷新父页面
                }
            });
        },'json');
        return false;
    });
    //选择时重载下拉框
    form.on('select(select)', function(data){
        form.render('select');
    });
    //但是，如果你的HTML是动态生成的，自动渲染就会失效
    //因此你需要在相应的地方，执行下述方法来进行渲染
    form.render();
$(function(){
	$('.layui-textarea').change(function(e){
		var val=$(this).val()
    $.ajax({
        url: '/urlinput',
        type: 'POST',
        dataType: 'json',
        async : true,
        data:{"val":val},
        success: function(result) {
          $('#title').val(result.Name)
          $('#client').val(result.Client)
          $('#client_id').val(result.Client_Id)
            if (result.push==false){
                $('.show-push').hide();
            }else{
                $('.show-push').show();
            }
        },
	    error: function(res){
        	alert("出错了- -")
        }
    });
	})
})
</script>
 </div>
  </div>
	 </body>
</html>