<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:93:"D:\phpstudy_pro\WWW\public/../application/promotion\view\promotion_rec\upd_promotion_rec.html";i:1602673229;s:20:"./static/header.html";i:1602579388;}*/ ?>
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



<form class="layui-form" action="">



<input type="hidden" name="id" value="<?php echo $list['Id']; ?>">



  <div class="layui-form-item">



    <label class="layui-form-label">账号备注:</label>



    <div class="layui-input-block" style="padding-top: 10px;">



   <?php echo $list['Remarks']; ?>



    </div>



  </div>



    <div class="layui-form-item">



    <label class="layui-form-label">推广账号:</label>



    <div class="layui-input-block" style="padding-top: 10px;">



  <?php echo $list['Pro_User']; ?>



    </div>



  </div>



  <div class="layui-form-item">



    <label class="layui-form-label">充值广告币:</label>



    <div class="layui-input-block">



      <input type="text" name="rec_b" value="<?php echo $list['Rec_B']; ?>" lay-verify="number" placeholder="请输入充值广告币" autocomplete="off" class="layui-input" <?php if(\think\Session::get('type')>2): ?>disabled<?php endif; ?>>



    </div>



  </div>



    <div class="layui-form-item">



    <label class="layui-form-label">充值人民币:</label>



    <div class="layui-input-block">



      <input type="text" name="money_rec" value="<?php echo $list['Money_Rec']; ?>" lay-verify="number" placeholder="请输入充值人民币" autocomplete="off" class="layui-input" <?php if(\think\Session::get('type')>2): ?>disabled<?php endif; ?>>



    </div>



  </div>



    <div class="layui-form-item">



    <label class="layui-form-label">充值时间:</label>



    <div class="layui-input-block">



       <input type="text" class="layui-input" name="cre_time" value="<?php echo date('Y-m-d H:m:s',$list['Cre_time']); ?>" id="test5" placeholder="y-m-d H:m:s" <?php if(\think\Session::get('type')>2): ?>disabled<?php endif; ?>>



    </div>



  </div>



  <div class="layui-form-item">



    <label class="layui-form-label">备注:</label>



    <div class="layui-input-block">



       <input type="text" class="layui-input" name="remarks" value="<?php echo $list['Remarks']; ?>">



    </div>



  </div>



  <div class="layui-form-item">

    <label class="layui-form-label">充值状态</label>

    <div class="layui-input-block">

      <input type="radio" name="status" value="0" title="未打款"<?php if(\think\Session::get('type')>2): ?>disabled <?php if($list['Status']=='0'): ?>checked<?php endif; else: if($list['Status']=='0'): ?>checked<?php endif; endif; ?> >

      <input type="radio" name="status" value="1" title="已打款" <?php if(\think\Session::get('type')>2): ?>disabled <?php if($list['Status']=='0'): ?>checked<?php endif; else: if($list['Status']=='1'): ?>checked<?php endif; endif; ?> >

      <input type="radio" name="status" value="2" title="已到账" <?php if($list['Status']=='2'): ?>checked<?php endif; ?>>

      <input type="radio" name="status" value="3" title="退款" <?php if($list['Status']=='3'): ?>checked<?php endif; ?>>

      <span style="color: red;size: 8px;">非管理员账号无法修改某些选项</span>

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



		$.post('/public/index.php/upd_promotion_rec_do',data.field,function(obj){



	        layer.alert(obj.msg,function(index){



	        	if(obj.code===0){

	             //window.parent.location.reload();//刷新父页面，这样直接刷新会导致检索条件也刷新

               parent.layui.table.reload("formfields_table");//所以还是刷新父页面的表格

              //关闭弹层

              var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引

              parent.layer.close(index); //再执行关闭

	        	}else{

              layer.close(index);

            }



	        });



		},'json');



		return false;



	});



	  //但是，如果你的HTML是动态生成的，自动渲染就会失效



	  //因此你需要在相应的地方，执行下述方法来进行渲染



	    //日期时间选择器



  laydate.render({



    elem: '#test5'



    ,type: 'datetime'



  });



	  form.render();



	});



</script>



</div>



</body>