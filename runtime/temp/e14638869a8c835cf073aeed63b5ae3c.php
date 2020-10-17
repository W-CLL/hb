<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:69:"D:\phpstudy_pro\WWW\public/../application/website\view\site\show.html";i:1597728200;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
  <style>
    .sitelist {
      height: 48px;
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 1;
    }

    .sitelist:hover {
      background-color: rgb(211, 208, 208);
    }

    .sitetitle {
      margin: 20px 0 10px 0;
      height: 35px;
      background-color: rgb(228, 226, 225);
    }

    .sitetitle strong {
      font-size: 20px;
      margin: auto;
      line-height: 35px;
      padding-left: 15px;
    }

    .siteicon {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      padding: 5px;
    }

    #siteclears {
      font-size: 30px;
      position: absolute;
      text-align: center;
      border: 1px solid rgba(104, 100, 100, 0.5);
      border-radius: 5px;
      padding: 3px;
      right: 30;
      top: 80;
    }

    #siteclears:hover {
      background-color: darkgrey;
      background-color: rgba(255, 0, 0, 0.8);
    }

    .siteclear {
      display: none;
      position: absolute;
      right: 0;
      top: 0;
      background-color: rgb(211, 208, 208);
    }

    .siteclear:hover {
      color: black;
      background-color: rgb(255, 0, 0, 0.6);
      z-index: 100;
    }

    #siteupdates {
      font-size: 30px;
      position: absolute;
      text-align: center;
      border: 1px solid rgba(104, 100, 100, 0.5);
      border-radius: 5px;
      padding: 3px;
      right: 30;
      top: 30;
    }

    #siteupdates:hover {
      /* background-color: darkgrey; */
      background-color: rgba(77, 151, 255, 0.8);
    }

    .siteupdate {
      display: none;
      position: absolute;
      right: 0;
      top: 0;
      background-color: rgb(211, 208, 208);
    }

    .siteupdate:hover {
      color: black;
      background-color: rgb(81, 165, 235);
      z-index: 100;
    }

    #baidusousuo {
      width: 100%;
      height: 50px;
      padding: 20px 0;
      text-align: center;
      line-height: 50px;
      /* border: 1px solid #777777; */
    }
  </style>



  <div class="layui-container">
    <div class="layui-row">
      <!-- 嵌入百度搜索 -->
      <form id="baidusousuo" action="https://www.baidu.com/s" target="_blank">
        <img src="http://www.baidu.com/img/baidu_jgylogo3.gif" alt="百度Logo" />
        <input type="text" name="wd" size="40" style="height: 38px;" />
        <input type="submit" value="百度一下"  style="height: 38px;padding: 5px;"/>
        <input type="hidden" name='ie' value="utf-8" />
        <input type="hidden" name="tn" value="ace" />
      </form>
    </div>

    <div class="sitetitle">
      <strong>公共导航</strong>
    </div>
    <div class="layui-row">
      <?php if(is_array($site_public) || $site_public instanceof \think\Collection || $site_public instanceof \think\Paginator): $i = 0; $__LIST__ = $site_public;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <a href="<?php echo $vo['Site_Url']; ?>" target="_blank">
        <div class="layui-col-md2 sitelist">
          <object data="" type="">
            <a href="" class="layui-icon siteclear" siteid='<?php echo $vo['Id']; ?>' public="1">&#x1006;</a>
          </object>
          <object data="" type="">
            <a href="" class="layui-icon siteupdate" siteid='<?php echo $vo['Id']; ?>' public="1">&#xe642;</a>
          </object>
          <img src="<?php echo $vo['Site_Icon']; ?>" alt="" class="siteicon"><?php echo $vo['Site_Name']; ?>
        </div>
      </a>
      <?php endforeach; endif; else: echo "" ;endif; ?>
      <!--新建按钮-->
      <a href="#" id="add_site_public">
        <div class="layui-col-md2 sitelist">
          <div class="siteicon" style="margin: 0 auto;">
            <i class="layui-icon" style="font-size: 36px;">&#xe624;</i>
          </div>
        </div>
      </a>
    </div>

    <?php if(\think\Session::get('type')<5): ?>
    <div class="sitetitle">
      <strong>内部导航</strong>
    </div>
    <div class="layui-row">
      <?php if(is_array($site_inside) || $site_inside instanceof \think\Collection || $site_inside instanceof \think\Paginator): $i = 0; $__LIST__ = $site_inside;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <a href="<?php echo $vo['Site_Url']; ?>" target="_blank">
        <div class="layui-col-md2 sitelist">
          <object data="" type="">
            <a href="" class="layui-icon siteclear" siteid='<?php echo $vo['Id']; ?>' public="-1">&#x1006;</a>
          </object>
          <object data="" type="">
            <a href="" class="layui-icon siteupdate" siteid='<?php echo $vo['Id']; ?>' public="-1">&#xe642;</a>
          </object>
          <img src="<?php echo $vo['Site_Icon']; ?>" alt="" class="siteicon"><?php echo $vo['Site_Name']; ?>
        </div>
      </a>
      <?php endforeach; endif; else: echo "" ;endif; ?>
      <!--新建按钮-->
      <a href="#" id="add_site_inside">
        <div class="layui-col-md2 sitelist">
          <div class="siteicon" style="margin: 0 auto;">
            <i class="layui-icon" style="font-size: 36px;">&#xe624;</i>
          </div>
        </div>
      </a>
    </div>
    <?php endif; ?>

    <div class="sitetitle">
      <strong>自定义导航</strong>
    </div>
    <div class="layui-row">
      <?php if(is_array($site_self) || $site_self instanceof \think\Collection || $site_self instanceof \think\Paginator): $i = 0; $__LIST__ = $site_self;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <a href="<?php echo $vo['Site_Url']; ?>" target="_blank">
        <div class="layui-col-md2 sitelist">
          <object data="" type="">
            <a href="" class="layui-icon siteclear" siteid='<?php echo $vo['Id']; ?>' public="0">&#x1006;</a>
          </object>
          <object data="" type="">
            <a href="" class="layui-icon siteupdate" siteid='<?php echo $vo['Id']; ?>' public="0">&#xe642;</a>
          </object>
          <img src="<?php echo $vo['Site_Icon']; ?>" alt="" class="siteicon"><?php echo $vo['Site_Name']; ?>
        </div>
      </a>
      <?php endforeach; endif; else: echo "" ;endif; ?>
      <a href="#" id="add_site">
        <div class="layui-col-md2 sitelist">
          <div class="siteicon" style="margin: 0 auto;">
            <i class="layui-icon" style="font-size: 36px;">&#xe624;</i>
          </div>
        </div>
      </a>
    </div>



  </div>
  <!-- 编辑图标 -->
  <div><span id="siteupdates" class="layui-icon">&#xe642;</span></div>
  <!-- 删除图标 -->
  <div><span id="siteclears" class="layui-icon">&#xe640;</span></div>


  <script>
    $('#add_site').click(function () {
      layer.open({
        type: 2,
        title: '添加网站',
        area: ['500px', '350px'],
        content: '/website/site/add_site/'
      });
    });

    $('#add_site_public').click(function () {
      layer.open({
        type: 2,
        title: '添加公共站点',
        area: ['500px', '350px'],
        content: '/website/site/add_site_public/'
      });
    });

    $('#add_site_inside').click(function () {
      layer.open({
        type: 2,
        title: '添加内部站点',
        area: ['500px', '350px'],
        content: '/website/site/add_site_inside/'
      });
    });

    $('.siteclear').click(function () {
      //自定义的属性，用来判断接受站点id和判断公共站点
      var siteid = $(this).attr('siteid');
      var public = $(this).attr('public');
      layer.confirm('确定删除？', function () {
        // console.log(siteid);
        $.post('/website/site/del_site/?siteid=' + siteid + '&public=' + public, function (res) {
          if (res.code == 0) {
            layer.msg(res.msg, { time: 2000 }, function () {
              location.reload()
            });
          } else {
            layer.open({ title: '提示信息', content: res.msg });
          }
        });
      });
      return false;
    })

    $('.siteupdate').click(function () {
      //自定义的属性，用来判断接受站点id和判断公共站点
      var siteid = $(this).attr('siteid');
      var public = $(this).attr('public');
      layer.open({
        type: 2,
        title: '修改网站',
        area: ['500px', '350px'],
        content: '/website/site/upd_site/?siteid=' + siteid + '&public=' + public
      });
      return false;
    })

    //删除站点图标
    $('#siteclears').click(function () {
      var status = $('.siteclear').css('display');
      if (status == 'none') {
        $('.siteclear').css('display', 'block');
        $('.siteupdate').css('display', 'none');
      } else {
        $('.siteclear').css('display', 'none');
      }
    });

    //编辑站点图标
    $('#siteupdates').click(function () {
      var status = $('.siteupdate').css('display');
      if (status == 'none') {
        $('.siteupdate').css('display', 'block');
        $('.siteclear').css('display', 'none');
      } else {
        $('.siteupdate').css('display', 'none');
      }
    });

  </script>
</body>

</html>