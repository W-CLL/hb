<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:79:"D:\phpstudy_pro\WWW\public/../application/project\view\project\ins_project.html";i:1602753379;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
  <form class="layui-form" action="">
    <div class="layui-form-item">
      <div class="layui-inline">
        <label class="layui-form-label">客户:</label>
        <div class="layui-input-inline">
          <select name="client_id" lay-verify="required" lay-filter="select">
            <option value="">请选择客户</option>
            <?php foreach($cli as $k=>$v): ?>
            <option value="<?php echo $v['User_Id']; ?>"><?php echo $v['Name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="layui-form-item">
      <label class="layui-form-label">项目等级:</label>
      <div class="layui-input-inline">
          <select name="projectgrade" lay-filter="select">
            <option value="A">A</option>
            <option value="B+">B+</option>
            <option value="B">B</option>
            <option value="C+">C+</option>
            <option value="C">C</option>
          </select>
      </div>
      <div class="layui-form-mid layui-word-aux">代表项目优先度</div>
    </div>

    <div class="layui-form-item">
      <div class="layui-inline">
        <label class="layui-form-label">负责人:</label>
        <div class="layui-input-inline">
          <select name="user_id" lay-verify="required" lay-filter="select">
            <option value="">请选择负责人</option>
            <?php foreach($per as $k=>$v): ?>
            <option value="<?php echo $v['User_Id']; ?>"><?php echo $v['Name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="layui-form-item">
      <div class="layui-inline ">
        <label class="layui-form-label">可见用户:</label>
        <div class="layui-input-inline" id="seeuser">
          <select name="see_user_id[]" lay-verify="required" class="seeuser" lay-filter="select">
            <option value="">请选择有权查看的用户</option>
            <?php foreach($per as $k=>$v): ?>
            <option value="<?php echo $v['User_Id']; ?>"><?php echo $v['Name']; ?></option>
            <?php endforeach; ?>
            <!-- 合作伙伴 -->
            <?php foreach($part as $k=>$v): ?>
            <option value="<?php echo $v['User_Id']; ?>"><?php echo $v['Name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button class="layui-btn" onclick="add('seeuser');" type="button">继续添加 </button>
        <button class="layui-btn layui-btn-danger" onclick="del('seeuser');" type="button">删除追加</button>
        <!-- <div class="layui-input-inline"></div>
        <div class="layui-input-inline"></div> -->
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">项目名称:</label>
      <div class="layui-input-inline">
        <input type="text" name="projectname" lay-verify="required" placeholder="请输入项目名" autocomplete="off"
          class="layui-input">
      </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-inline">
        <label class="layui-form-label">时间范围</label>
        <div class="layui-input-inline">
          <input type="text" class="layui-input" id="test9" placeholder=" - " name="time" lay-verify="required">
        </div>
      </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-inline ">
        <label class="layui-form-label">推广账号:</label>
        <div class="layui-input-inline" id="promotion">
          <select name="pro_user_id[]" lay-verify="required" class="promotion" lay-filter="select">
            <option value="">请选择推广账号</option>
            <?php foreach($pro_user as $k=>$v): ?>
            <option value="<?php echo $v['Id']; ?>"><?php echo $v['Pro_User']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button class="layui-btn" onclick="add('promotion');" type="button">继续添加 </button>
        <button class="layui-btn layui-btn-danger" onclick="del('promotion');" type="button">删除追加</button>
        <!-- <div class="layui-input-inline"></div>
          <div class="layui-input-inline"></div> -->
      </div>
    </div>

    </div>
    <div class="layui-form-item">
      <div class="layui-inline">
        <label class="layui-form-label">53账号:</label>
        <div class="layui-input-inline" id="user_53">
          <select name="user_53_id[]" lay-verify="required" class="user_53" lay-filter="select">
            <option value="">请选择53账号</option>
            <?php foreach($user_53 as $k=>$v): ?>
            <option value="<?php echo $v['Id']; ?>"><?php echo $v['User_53']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button class="layui-btn" onclick="add('user_53');" type="button">继续添加 </button>
        <button class="layui-btn layui-btn-danger" onclick="del('user_53');" type="button">删除追加</button>
        <!-- <div class="layui-input-inline">
        </div>
        <div class="layui-input-inline">
          </div> -->
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">项目53代码:</label>
      <div class="layui-input-inline">
        <input type="text" name="code_53" lay-verify="required" placeholder="请输入项目53代码" autocomplete="off"
          class="layui-input">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">目标成本:</label>
      <div class="layui-input-inline">
        <input type="text" name="estimatedcost" lay-verify="required|number" placeholder="请输入目标成本" autocomplete="off"
          class="layui-input">
      </div>
    </div>

    <div class="layui-form-item">
      <label class="layui-form-label">目标数量:</label>
      <div class="layui-input-inline">
        <input type="text" name="targetnumber" lay-verify="number" placeholder="请输入目标数量" autocomplete="off"
          class="layui-input">
      </div>
    </div>

    <div class="layui-form-item">
      <label class="layui-form-label">预算:</label>
      <div class="layui-input-inline">
        <input type="text" name="customerbudget" lay-verify="required|number" placeholder="请输入客户预算" autocomplete="off"
          class="layui-input">
      </div>
    </div>
    
    <div class="layui-form-item">
      <label class="layui-form-label">品牌:</label>
      <div class="layui-input-inline">
        <input type="text" name="brand" lay-verify="" placeholder="请输入品牌" autocomplete="off" class="layui-input">
      </div>
    </div>



    <div class="layui-form-item">
      <label class="layui-form-label">地域:</label>
      <div class="layui-input-inline">
        <input type="text" name="address" lay-verify="" placeholder="请输入地域" autocomplete="off" class="layui-input">
      </div>
    </div>






    
    <div class="layui-form-item">
      <label class="layui-form-label">备注:</label>
      <div class="layui-input-inline">
        <input type="text" name="remarks" lay-verify="" placeholder="请输入备注" autocomplete="off" class="layui-input">
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
    layui.use('form', function () {

      var layer = layui.layer, $ = layui.$, laydate = layui.laydate, form = layui.form;//只有执行了这一步，部分表单元素才会自动修饰成功
      //追加按钮
      add = function (val) {
        $('#' + val).children(":first").clone(true).appendTo('#' + val)

        form.render();

      }
      //删除操作
      del = function (val) {
        if ($('#' + val).children().length == "1") {
          layer.alert("没有可删除的节点了")
        } else {
          $('#' + val).children("." + val).remove()
          $('#' + val).children(":last").remove()
        }
        form.render();
      }
      //表单提交
      form.on('submit(add)', function (data) {
        $.post('/public/index.php/ins_project_do', data.field, function (obj) {
          layer.alert(obj.msg, function () {
            if (obj.code === 0) {
              window.parent.location.reload();//刷新父页面
            }
          });
        }, 'json');
        return false;
      });
      form.on('select(select)', function (data) {
        form.render('select');
      });

      //但是，如果你的HTML是动态生成的，自动渲染就会失效
      //因此你需要在相应的地方，执行下述方法来进行渲染
      //时间范围
      laydate.render({
        elem: '#test9'
        , type: 'time'
        , range: true
      });
      form.render();
    });

  </script>
</body>