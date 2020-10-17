<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:70:"D:\phpstudy_pro\WWW\public/../application/user\view\user\ins_user.html";i:1602664986;s:20:"./static/header.html";i:1602579388;}*/ ?>
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

<body centent="centent">

	<div class="layui-container">

<form class="layui-form" action="">

  <div class="layui-form-item">

    <div class="layui-inline">

      <label class="layui-form-label">账号类型:</label>

      <div class="layui-input-block">

        <select name="type_id" lay-verify="required" lay-filter="select">

          <option value="">请选择账号类型</option>

          <?php foreach($type_name as $k=>$v): ?>

          <option value="<?php echo $v['Type_Id']; ?>"><?php echo $v['Type_Name']; ?></option>

          <?php endforeach; ?>

        </select>

      </div>

    </div>

</div>

  <div class="layui-form-item">

    <label class="layui-form-label">姓名:</label>

    <div class="layui-input-block">

      <input type="text" name="name" lay-verify="required" placeholder="请输入用户姓名" autocomplete="off" class="layui-input">

    </div>

  </div>

  <div class="layui-form-item">

    <label class="layui-form-label">账号:</label>

    <div class="layui-input-block">

      <input type="text" name="user" lay-verify="required" placeholder="请输入账号" autocomplete="off" class="layui-input">

    </div>

  </div>

  <div class="layui-form-item">

    <label class="layui-form-label">密码:</label>

    <div class="layui-input-block">

      <input type="text" name="psw" lay-verify="required" placeholder="请输入用户密码" autocomplete="off" class="layui-input">

    </div>

  </div>

  <div class="layui-form-item">

    <label class="layui-form-label">别名:</label>

    <div class="layui-input-block">

      <input type="text" name="alias" lay-verify="" placeholder="请输入用户别名" autocomplete="off" class="layui-input">

    </div>

  </div>

  <div class="layui-form-item">

    <label class="layui-form-label">手机号码:</label>

    <div class="layui-input-block">

      <input type="text" name="phone" lay-verify="" placeholder="请输入11位手机号" autocomplete="off" class="layui-input">

    </div>

  </div>

  <div class="layui-form-item">

    <div class="layui-input-block">

      <button type="submit" class="layui-btn" lay-submit="" lay-filter="add">立即提交</button>

      <button type="reset" class="layui-btn layui-btn-primary">重置</button>

    </div>

  </div>

</form>

<script type="text/javascript">

layui.use('form', function(){



	var	layer = layui.layer,$=layui.$,laydate = layui.laydate,form = layui.form;//只有执行了这一步，部分表单元素才会自动修饰成功

	//表单提交

	form.on('submit(add)',function(data){

		$.post('/public/index.php/ins_user_do',data.field,function(obj){

	        layer.alert(obj.msg,function(){

	        	if(obj.code===0){

              //window.parent.location.reload();//刷新父页面，这样直接刷新会导致检索条件也刷新
              parent.layui.table.reload("formfields_table");//所以还是刷新父页面的表格
                //关闭弹层
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layer.close(index); //再执行关闭

	        	}

	        });

		},'json');

		return false;

	});

  form.on('select(select)', function(data){

    form.render('select');

  });

	  //但是，如果你的HTML是动态生成的，自动渲染就会失效

	  //因此你需要在相应的地方，执行下述方法来进行渲染

	  form.render();

	});

</script>

</div>

</body>