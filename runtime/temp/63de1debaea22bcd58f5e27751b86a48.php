<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"D:\phpstudy_pro\WWW\public/../application/login\view\register\register.html";i:1600676279;}*/ ?>
<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <title>注册用户</title>

    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" media="screen" href="static/login/style.css">
    <link rel="stylesheet" type="text/css" href="static/login/reset.css">

    <link rel="icon" href="/favicon.ico" type="img/x-ico" />
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <!-- <link rel="stylesheet" href="/static/css/login.css"> -->

</head>

<style>
    .layui-form-item {
        margin-top: 20px;
    }
</style>

<body>

    <div id="particles-js">
        <div class="layui-container laymy-login">
            <div class="layui-row login" style="height: 600px;">
                <div class="layui-col-lg7 layui-col-md7">
                    <div class="laymy-login-title"></div>
                </div>
                <div class="layui-col-lg10 layui-col-md10 layui-col-md-offset1">
                    <div class="laymy-login-font" style="font-size:26px;margin: 40px 0">用户注册</div>
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-icon layui-form-label layui-icon-username laymy-icon laymy-login-blue"
                                style="width: 5%;float: left;padding: 9px 5px;"></label>
                            <input type="text" name="user" required lay-verify="required|user" placeholder="请输入账号"
                                style="width:90%; float:right;" autocomplete="off" class="layui-input">
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-icon layui-form-label layui-icon-password laymy-icon laymy-login-blue"
                                style="width: 5%;float: left;padding: 9px 5px;"></label>
                            <input type="password" name="password" required lay-verify="required|repassword"
                                placeholder="请输入密码" style="width:90%; float:right;" autocomplete="off"
                                class="layui-input">
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-icon layui-form-label layui-icon-password laymy-icon laymy-login-blue"
                                style="width: 5%;float: left;padding: 9px 5px;"></label>
                            <input type="password" name="repassword" required lay-verify="required|repassword"
                                placeholder="请确认密码" style="width:90%; float:right;" autocomplete="off"
                                class="layui-input">
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-icon layui-form-label layui-icon-username laymy-icon laymy-login-blue"
                                style="width: 5%;float: left;padding: 9px 5px;"></label>
                            <input type="text" name="username" required lay-verify="required|username"
                                placeholder="请输入姓名" style="width:90%; float:right;" autocomplete="off"
                                class="layui-input">
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-icon layui-form-label layui-icon-username laymy-icon laymy-login-blue"
                                style="width: 5%;float: left;padding: 9px 5px;"></label>
                            <input type="text" name="alias" required lay-verify="required|alias" placeholder="请输入公司名"
                                style="width:90%; float:right;" autocomplete="off" class="layui-input">
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-icon layui-form-label layui-icon-cellphone laymy-icon laymy-login-blue"
                                style="width: 5%;float: left;padding: 9px 5px;"></label>
                            <input type="text" name="phone" required lay-verify="required|phone|number"
                                placeholder="请输入手机号" style="width:90%; float:right;" autocomplete="off"
                                class="layui-input">
                        </div>

                        <div class="layui-form-item">
                            <div id="slider" class="layui-input"></div>
                        </div>
                        <div class="layui-form-item">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="lay-submit-login"
                                style="width: 100%;">注册</button>
                        </div>
                    </form>
                    <div style="float: right;"><a href="/login">返回登录</a></div>
                </div>
            </div>
        </div>
        <div class="sk-rotating-plane"></div>
        <canvas class="particles-js-canvas-el" width="1920" height="888" style="width: 100%; height: 100%;"></canvas>
    </div>
    <!-- scripts -->
    <script src="static/login/particles.min.js"></script>
    <script src="static/login/app.js"></script>
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

    <script src="/static/layui/layui.all.js"></script>
    <script src="/static/layui/sliderVerify.js"></script>

    <script>
        var form = layui.form, $ = layui.$, sliderVerify = layui.sliderVerify;

        var slider = sliderVerify.render({
            elem: '#slider',
        })

        //监听表单提交
        form.on('submit(lay-submit-login)', function (data) {
            //对提交数据验证
            if (data.field.password != data.field.repassword) {
                layer.msg('两次输入密码不一致', { icon: 5 });
                return false;
            }
            if (slider.isOk()) {
                $.post("/register", data.field, function (data) {
                    layer.msg(data.msg, { icon: 1, time: 2000 }, function () {
                        if (data.code == 0) {
                            window.location.href = "/login";
                        }
                    });
                }, 'json');
            }
            return false;
        });

        //自定义表单验证
        form.verify({
            username: function (value, item) {
                var hanzi = /^[\u4e00-\u9fa5]+$/;
                if (value.length < 2 || value.length >= 5) {
                    return '姓名长度不符';
                } else if (!hanzi.test(value)) {
                    return '姓名只支持汉字';
                }
            },
            alias: function (value, item) {
                var hanzi = /^[\u4e00-\u9fa5]+$/;
                if (!hanzi.test(value)) {
                    return '公司名称只支持汉字';
                }
            },
            repassword: function (value, item) {
                var ren = /(?=.*[0-9])(?=.*[a-zA-Z]).{6,30}/;
                if (!ren.test(value)) {
                    return "密码复杂度太低（密码中必须同时包含字母、数字，且大于6个字符）";
                }
            },
            user: function (value, item) {
                var ren = /[0-9a-zA-Z]{2,23}$/;
                if (!ren.test(value)) {
                    return '账号只支持数字和字母，且大于2个字符';
                }
            }
        });
    </script>
</body>

</html>