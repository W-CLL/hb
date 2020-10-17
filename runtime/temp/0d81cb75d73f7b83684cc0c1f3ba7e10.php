<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"D:\phpstudy_pro\WWW\haibao1\public/../application/login\view\login\login.html";i:1600679295;}*/ ?>

<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<title>
		<?php switch($name = $_SERVER['HTTP_HOST']): case "www.test.com": ?>留言管理系统<?php break; default: ?>海豹广告管理系统
		<?php endswitch; ?>
	</title>

	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="stylesheet" media="screen" href="static/login/style.css">
	<link rel="stylesheet" type="text/css" href="login/reset.css">

	<link rel="icon" href="/favicon.ico" type="img/x-ico" />
	<link rel="stylesheet" href="/public/static/layui/css/layui.css">
	<!-- <link rel="stylesheet" href="/public/static/css/login.css"> -->

</head>

<style>
	.layui-form-item{
		margin-top: 30px;
	}
	#wx_login{
		position: absolute;
		right: -20px;
		top: 10px;
		font-size: 38px;
		cursor:pointer;
		color: rgb(100, 134, 67);
	}
</style>

<body>

	<div id="particles-js">
		<div class="layui-container laymy-login">
			<div class="layui-row login">
				<div class="layui-col-lg7 layui-col-md7">
					<div class="laymy-login-title"></div>
				</div>
				<div class="layui-col-lg10 layui-col-md10 layui-col-md-offset1">
					<div class="laymy-login-font" style="font-size:26px;margin: 40px 0">用户登录</div>
					<div id="wx_login" class="layui-icon layui-icon-login-wechat" onclick="wx_login()"></div>
					<form class="layui-form" action="">
						<div class="layui-form-item">
							<label class="layui-icon layui-form-label layui-icon-username laymy-icon laymy-login-blue"
								style="width: 5%;float: left;padding: 9px 5px;"></label>
							<input type="text" name="username" required lay-verify="required" placeholder="请输入用户名"
								style="width:90%; float:right;" autocomplete="off" class="layui-input">
						</div>

						<div class="layui-form-item">
							<label class="layui-icon layui-form-label layui-icon-password laymy-icon laymy-login-blue"
								style="width: 5%;float: left;padding: 9px 5px;"></label>
							<input type="password" name="password" required lay-verify="required" placeholder="请输入密码"
								style="width:90%; float:right;" autocomplete="off" class="layui-input">
						</div>

						<div class="layui-form-item">
							<div id="slider" class="layui-input"></div>
						</div>
						<div class="layui-form-item">
							<button class="layui-btn layui-btn-normal" lay-submit lay-filter="lay-submit-login" style="width: 100%;">登录</button>
						</div>
					</form>
					<div style="float: left;"><a href="javascript:;" onclick="forgetpw()">忘记密码？</a></div>
					<div style="float: right;"><a href="/public/index.php/register">注册</a></div>
				</div>
			</div> 
		</div>
		<div class="sk-rotating-plane"></div>
		<canvas class="particles-js-canvas-el" width="1920" height="888" style="width: 100%; height: 100%;"></canvas>
	</div>
	<div style="width: 100%;text-align: center;position: absolute;bottom: 10px;color: rgba(255, 255, 255, 0.6);">
		<p>
			<!-- 底部固定区域 -->
			© s.ykhwzx.cn - 广州海豹数字科技有限公司 | 粤ICP备19023107号-8
		</p>

	</div>

	<!-- scripts -->
	<script src="static/login/particles.min.js"></script>
	<script src="login/app.js"></script>
	<!-- 动态背景 -->
	<script type="text/javascript">
		function hasClass(elem, cls) {
			cls = cls || '';
			if (cls.replace(/\s/g, '').length == 0) return false; //当cls没有参数时，返回false
			return new RegExp(' ' + cls + ' ').test(' ' + elem.className + ' ');
		}

		function addClass(ele, cls) {
			if (!hasClass(ele, cls)) {
				ele.className = ele.className == '' ? cls : ele.className + ' ' + cls;
			}
		}

		function removeClass(ele, cls) {
			if (hasClass(ele, cls)) {
				var newClass = ' ' + ele.className.replace(/[\t\r\n]/g, '') + ' ';
				while (newClass.indexOf(' ' + cls + ' ') >= 0) {
					newClass = newClass.replace(' ' + cls + ' ', ' ');
				}
				ele.className = newClass.replace(/^\s+|\s+$/g, '');
			}
		}
	</script>

	<script src="/public/static/layui/layui.all.js"></script>
	<script src="/public/static/layui/sliderVerify.js"></script>

	<script>
		var form = layui.form, $ = layui.$, sliderVerify = layui.sliderVerify;

		var slider = sliderVerify.render({
			elem: '#slider',
		})

		form.on('submit(lay-submit-login)', function (data) {
			if (slider.isOk()) {
				$.post("index.php/checklogin", data.field, function (data) {
					layer.msg(data.msg, function () {
						if (data.code == 1) {
							window.location.href = "index.php/index";
						}
					});
				}, 'json');
			}
			return false;
		});

		function forgetpw(){
			layer.open({
				content:'<div style="text-align:center;">暂不支持自主找回密码，忘记密码请联系管理员<br><img src="localhost/public/static/images/weixing.jpg" style="width:128px;height:128px;"></div>'
			})
		}
		function wx_login(){
			layer.open({
				content:''
			})
		}
	</script>
</body>

</html>