<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:70:"D:\phpstudy_pro\WWW\public/../application/user\view\role\leftmenu.html";i:1602666069;s:20:"./static/header.html";i:1602579388;}*/ ?>
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
<script src="/public/static/js/vue.min.js"></script>

<body>
    <div id="app" style="padding: 20;">
        <form action="" id='menulist'>

            <input type="checkbox" id="checkall" v-model="checkall" v-on:click="check_all">
            <label for="checkall">全选</label>
            <hr>

            <div v-for="(item, index) in leftmenu">

                <input type="checkbox" v-bind:id="index" v-model="leftmenu[index].hidden">
                <label v-bind:for="index">{{ item.name }}</label>

                <div v-for="(item2,index2) in item.list" style="padding-left: 20;">
                    <input type="checkbox" v-bind:id="index + '_' + index2"
                        v-model="leftmenu[index].list[index2].hidden">
                    <label v-bind:for="index + '_' + index2">{{ item2.name }}</label>
                </div>

            </div>
            <hr>
            <div>
                <button v-on:click="subm" class="layui-btn layui-btn-normal">提交</button>
            </div>

        </form>
    </div>

    <script>
        var leftmenu = <?php echo $leftmenu; ?>

        var app = new Vue({
            el: "#app",
            data: { "leftmenu": leftmenu, group_id: '<?php echo $group_id; ?>', checkall: false },
            methods: {
                subm: function () {
                    var json = []
                    //将菜单状态保存到json
                    this.leftmenu.forEach((item, index) => {
                        json.push({ "hidden": item.hidden, "list": [] })
                        item.list.forEach((item2, index2) => {
                            json[index].list.push({ 'hidden': item2.hidden })
                        })
                    })
                    var json = JSON.stringify(json);
                    $.post('/public/index.php/leftmenu/type/group_id/' + this.group_id, { data: json }, function (res) {
                        if (res.code == 0) {
                            alert(res.msg)
                        }
                    }, 'json')
                },
                //全选
                check_all: function () {
                    this.leftmenu.forEach((element) => {
                        element.hidden = !this.checkall
                        element.list.forEach(item => {
                            item.hidden = !this.checkall
                        });
                    });
                }
            },
        })

        // //获取基础菜单列表
        // $.ajax({
        //     url: '/static/json/leftmenu/basemenu.json',
        //     type: 'get',
        //     dataType: 'json',
        //     success: function (res) {
        //         app.leftmenu = res;
        //     },
        //     error: function (res) {
        //         console.log(res.status)
        //     }
        // });

        // //获取对应角色菜单状态
        // $.ajax({
        //     url: '/static/json/leftmenu/<?php echo $group_id; ?>.json',
        //     type: 'get',
        //     dataType: 'json',
        //     success: function (res) {
        //         if (res) {
        //             res.forEach((item, index) => {
        //                 app.leftmenu[index].hidden = item.hidden
        //                 item.list.forEach((item2, index2) => {
        //                     app.leftmenu[index].list[index2].hidden = item2.hidden
        //                 })
        //             });
        //         }
        //     },
        //     error: function (res) {
        //         console.log(res.status)
        //     }
        // });
    </script>
</body>