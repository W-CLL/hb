<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"D:\phpstudy_pro\WWW\public/../application/index\view\index\index.html";i:1602839775;}*/ ?>
<!-- <!DOCTYPE html> -->
<html>
<head>
	<meta charset="utf-8">
	<title>
		<?php switch($name = $_SERVER['HTTP_HOST']): case "www.test.com": ?>留言管理系统<?php break; default: ?>海豹广告管理系统
		<?php endswitch; ?>
	</title>
	<meta name="renderer" content="webkit">
	<link rel="icon" href="/favicon.ico" type="img/x-ico" />
	<link rel="stylesheet" href="/public/static/layui/css/layui.css">
	<link rel="stylesheet" href="/public/static/css/index.css">
</head>
<body>

<div class="layui-layout layui-layout-admin">
	  
	<div class="layui-header header header-demo">
		<ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
			<li class="layui-nav-item" lay-unselect="">
				<input type="hidden" name="color" value="" id="test-all-input">
				<div id="test-all"></div>
			</li>
			
			<li class="layui-nav-item" lay-unselect="">
				<a href="javascript:;">
					<cite><i class="layui-icon nav-icon">&#xe640;</i></cite>
					<span class="layui-nav-more"></span></a>
				<dl class="layui-nav-child layui-anim layui-anim-upbit">
					<dd layadmin-event="clearRedis" style="text-align: center;">
						<a href="javascript:;" onclick="clearRedis('list');"> 清除列表缓存</a>
					</dd>
					<dd layadmin-event="clearRedis" style="text-align: center;">
						<a href="javascript:;" onclick="clearRedis('all');"> 清除全部缓存</a>
					</dd>
				</dl>
			</li>

			<li class="layui-nav-item" lay-unselect="">
				<a href="javascript:;">
					<cite><?php echo \think\Session::get('username'); ?></cite>
					<span class="layui-nav-more"></span></a>
				<dl class="layui-nav-child layui-anim layui-anim-upbit">

					<dd><a href="javascript:;" onclick="
								layer.open({
									type:2,
									title:'添加',
									area:['50%','400px'],
									shadeClose:true,//外部关闭
									content:'/public/index.php/upd_msg'
								});  ">
								修改密码
						</a>
					</dd>
					<!-- <hr> -->
					<dd>
						<a href="javascript:;" onclick="bindwx()"> 绑定微信</a>
						<div id="qrcode" style="display: none;"></div>
					</dd>
					<!-- <hr> -->
					<dd layadmin-event="logout" style="text-align: center;">
						<a href="/public/index.php/loginout"> 退出</a>
					</dd>
				</dl>
			</li>
		</ul>

    </div>
    
	<div class="layui-side">
		<div class="layui-logo" lay-href="" style="width:260px">
			<span style="font-size: 24px;color:white;">
				<a href="/public/index.php/index" style="color: white;">办公自动化管理系统</a>
			</span>
		</div>

		
		<ul class="layui-nav layui-nav-tree" lay-filter="allmenu">
			<!-- 侧边导航-->
            <!-- <script src="/static/js/leftMenu.js"></script> -->

            <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['hidden']): ?>
                <li class="layui-nav-item" >
                    <a href="javascript:;" class="nav-hade <?php if((empty($vo['list']))): ?> laymy-menu <?php endif; ?>" data-href="<?php echo $vo['url']; ?>">
                        <i class="layui-icon nav-icon"><?php echo $vo['icon']; ?></i><?php echo $vo['name']; ?>
                    </a>
                    <?php if((!empty($vo['list']))): ?>
                    <dl class="layui-nav-child">
						<?php if(is_array($vo['list']) || $vo['list'] instanceof \think\Collection || $vo['list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;if($list['hidden']): ?>
						<dd><a href="javascript:;" class="laymy-menu" data-href="<?php echo $list['url']; ?>"><?php echo $list['name']; ?></a></dd>
						<?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                    <?php endif; ?>
                </li>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        
	</div>

    <div class="layui-tab layui-tab-card" lay-filter="demo" lay-allowclose="true">
        <ul class="layui-tab-title">
			<li class="layui-this" lay-id='main'>首页</li>
        </ul>
        <div class="layui-tab-content layui-body">
			<audio src="/public/music/tip.mp3"  hidden="hidden" id="audio"></audio>
			<audio src="/public/music/target.mp3"  hidden="hidden" id="target"></audio>
            <div class="layui-tab-item layui-show">
                <iframe class="laymy-iframe" frameborder='0' src="/public/index.php/main" lay-filter="main"></iframe>
            </div>
        </div>
    </div>
	
</div>

<script src="/public/static/layui/layui.all.js" ></script>
<script src="http://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
<script src='http://cdn.bootcss.com/socket.io/1.3.7/socket.io.js'></script>

<script>
 	layui.use('element', function () {
		var element = layui. element ,$ = layui.$ ,colorpicker = layui.colorpicker;
		//一些事件监听
		// element.on('tab(demo)', function (data) {
		// 	console.log(data);
		// });
		//删除指定lay-id="yyy"的这一项
		// element.tabDelete('demo', 'dindan');

		$(".laymy-menu").click(function () {
			//这里获取的如data-href="/user"
			var iframesrc = $(this).attr("data-href");
			//点击菜单的时候检查顺便检查下有没有红点的消息提醒
			dot(this);
			var title = $(this).text();
			//如果iframesrc得到了/user,则lay_id获得user,即去掉/
			var lay_id = iframesrc.slice(1);

			//如果有这个选项卡就切换到这个，没有就新增一个
			if ($("li[lay-id='" + lay_id + "']").length) {
				// 用于外部切换到指定的Tab项上，切换到 lay-id="yyy" 的这一项
				element.tabChange('demo', lay_id);
				// 然后刷新
				$("iframe[lay-filter='"+lay_id+"']").attr('src',iframesrc);
			} else {
				// 用于新增一个Tab选项,然后切换到那个去
				element.tabAdd('demo', {
					title: title
					, content: '<iframe src="'+iframesrc+'" frameborder="0" lay-filter="' + lay_id + '" style="width:100%;height:100%"></iframe>'
					, id: lay_id //lay-id="yyy" 的这一项
				});
				element.tabChange('demo', lay_id);
			}
		})
		//开启全功能
		colorpicker.render({
			elem: '#test-all'
			,color: 'rgba(50,50,50)'
			,format: 'rgb'
			,predefine: true
			,alpha: true
			,done: function(color){
				$('#test-all-input').val(color); //向隐藏域赋值
				layer.tips('给指定隐藏域设置了颜色值：'+ color, this.elem);
				
				color || this.change(color); //清空时执行 change
			}
			,change: function(color){
				//给当前页面头部和左侧设置主题色
				$('.header-demo,.layui-side,.layui-side .layui-nav').css('background-color', color);
				window.localStorage.setItem('bgcolor', color);
			}
		});
	});

    var win = document.title
    var windowmsg = {
        time: 0,
        title: document.title,
        timer: null,
        //显示新消息提示
        show: function () {
            var title = windowmsg.title.replace("", "").replace("【您有新消息】", "");
            //定时器，此处产生闪烁
            //由于定时器无法清除，在此调用之前先主动清除一下定时器打到缓冲效果，否则定时器效果叠加标题闪烁频率越来越快
            clearTimeout(windowmsg.timer);
            windowmsg.timer = setTimeout(function () {
                windowmsg.time++;
                windowmsg.show();
                if (windowmsg.time % 2 == 0) {
                    document.title = "【您有新消息】" + title
                } else {
                    document.title = title
                };
            }, 300);
            return [windowmsg.timer, windowmsg.title];
        },
        //取消新消息提示
        //此处起名最好不要用clear，由于关键字问题有时可能会无效
        clears: function () {
            clearTimeout(windowmsg.timer);
            document.title = win
        }
    };
	jQuery(function ($) {
		//play是原生js方法，jq对象没有
		var tip=document.getElementById('audio');
		// 连接服务端
		// var socket = io('http://s.ykhwzx.cn:2120'); //这里当然填写真实的地址了
		var socket = io('http://127.0.0.1:2120');
		// uid可以是自己网站的用户id，以便针对uid推送以及统计在线人数
		uid = '400'+<?php echo \think\Session::get('id'); ?>+'009';
		// socket连接后以uid登录
		socket.on('connect', function () {
			socket.emit('login', uid);
		});

		// 后端推送来消息时
		socket.on('new_msg', function (msg) {
			if(msg == '绑定成功'){
				layer.open({
					title:msg,
					content:'已完成微信绑定，可以使用相关功能了',
					end:function(){
						location.reload();
					}
				})
			}else{
				windowmsg.show();
				tip.play();
				layer.open({
					title:'系统提醒',
					content: msg,
					shadeClose:true,
					end:function(){
						windowmsg.clears();
					}
				})
				console.log("收到消息：" + msg);
			}
		});

		// 后端推送来在线数据时
		<?php if(\think\Session::get('auth')<3): ?>
		socket.on('update_online_count', function (online_stat) {
			console.log(online_stat);
		});
		<?php endif; ?>

		//后端推送提示信息时
		socket.on('tip',function(msg){
			switch(msg.active){
				case 'remind'://红点消息提醒
					console.log(msg);
					var $ = layui.$;
					//根据route定义数据找到的DOM元素，子菜单，和父菜单
					var sub_menu = $('[data-href="'+msg.route+'"]').parent()
					var menu = sub_menu.parent().prev();
					
					//然后添加红点提示
					var dot1 = '<i class="dot1" route="'+msg.route+'"></i>';
					var dot2 = '<i class="dot2">'+msg.num+'</i>';
					menu.append(dot1);
					sub_menu.append(dot2);
					break;
				case 'open_new'://弹出框消息提醒
					console.log(msg.msg);
					windowmsg.show();
					tip.play();
					layer.open({
						title: '系统提醒',
						content: msg.msg,
						shadeClose: true,
						end: function () {
							windowmsg.clears();
						}
					})
					break;
			}
		})
	})
</script>

<script>
	// 点击按钮清除缓存的ajax请求
	function clearRedis(scene)
	{
		function to_do(scene){
			$.post('clear_redis?scene='+scene,function(data){
				layer.msg(data,{time:2000},function(){
					location.href = '/public/index.php/index'
				});
			});
		}

		if(scene == 'all'){
			layer.confirm('确定清除全部缓存?', {icon: 3, title:'提示'}, function(index){
				//do something
				to_do(scene);
			});
			layer.close(index);
		}else{
			to_do(scene);
		}
	}

	//绑定微信
	function bindwx(){
		layer.open({
			area:['450px','400px'],
			type:2,
			content:'/bindwx'
		})
	}


	//打开浏览器获取红点消息提示缓存
	(function(){
		$.get('/public/index.php/push/push/check_dot',function(res){
			// console.log(res.data);
			let obj = res.data;
			for(k in obj){
				//根据route定义数据找到的DOM元素，子菜单，和父菜单
				let sub_menu = $('[data-href="'+obj[k]['route']+'"]').parent()
				let menu = sub_menu.parent().prev();
				
				//然后添加红点提示
				let dot1 = '<i class="dot1" route="'+obj[k]['route']+'"></i>';
				let dot2 = '<i class="dot2">'+obj[k]['num']+'</i>';
				menu.append(dot1);
				sub_menu.append(dot2);
			}
		},'json')
		//设置背景颜色
		try{
			let color = window.localStorage.getItem('bgcolor');
			$('.header-demo,.layui-side,.layui-side .layui-nav').css('background-color', color);
		}catch(err){
			console.log('没有设置bgcolor变量');
		}
	})();

	//点击菜单后，清除红点消息提醒
	function dot(i){
		let route = $(i).attr("data-href");
		let dot = $(i).siblings("i");
		// 如果不存在红点就返回
		if(!dot.length){
			return false;
		}
		//如果存在红点就删除
		dot.remove();
		$('i[route="'+route+'"]').remove();
		//还要通知服务器删除红点的缓存
		$.post('/public/index.php/push/push/rm_dot',{route:route},function(res){
			console.log(res);
		},'json');
	}

</script>

<!-- <script src="/static/js/process.js"></script> -->

<?php if(\think\Session::get('type')<=4): ?>
<script src="/public/static/js/Cache.js"></script>
<script>
	//工作目标消息超时提醒
	setInterval(function () {
		try{
			//此标签页下不再提示
			var noRemind = JSON.parse(Cache().get('noRemind'));
			if(noRemind){
				return false;
			}
			var timeRemind = Cache().get('timeRemind')
			//指定时间之内不提示
			if(new Date().getTime() < timeRemind){
				return false;
			}
		}catch(err){
			console.log(err);
		}
		var targetRemind = JSON.parse(Cache('local').get('targetRemind'));
		
		var count = 0;
		targetRemind.forEach(item => {
			var end_time = new Date(item.End_time)
			var now_time = new Date()
			if(now_time > end_time && item.User_Id == <?php echo \think\Session::get('id'); ?>){
				count = count + 1
			}
		});

		if( count >= 1 ){
			windowmsg.show();
			document.getElementById('target').play();
			layer.open({
				btn: ['查看', '关闭', '不再提示']
				,title: '未完成目标提醒'
				,content: '你有' + count + '个目标超过截止时间'
				,yes: function(index, layero){
					$('iframe').attr('src','target');
					layer.close(index);
				}
				,btn2: function(index, layero){
					//指定时间内不提示
					Cache().set('timeRemind',(new Date().getTime()+(600000)))
					//return false 开启该代码可禁止点击该按钮关闭
				}
				,btn3: function(index, layero){
					//按钮【按钮三】的回调
					Cache().set('noRemind',true)
					//return false 开启该代码可禁止点击该按钮关闭
				}
				,cancel: function(){
					//右上角关闭回调
					//return false 开启该代码可禁止点击该按钮关闭
				},
				end: function () {
					windowmsg.clears();
				}
			})
		}
	}, 6000)
</script>
<?php endif; if((\think\Session::get('type')<=4) And \think\Session::get('open_window')): ?>
<script>
	layer.open({
		content:'修改这里的内容'
	})
</script>
<?php elseif((\think\Session::get('type')>4) And \think\Session::get('open_window')): ?>
<script>
	layer.open({
		content:'对外弹窗内容',
	})
</script>
<?php endif; session('open_window', null) ?>

</body>
</html>