<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"D:\phpstudy_pro\WWW\public/../application/api\view\wo_long\ocpc.html";i:1597728236;}*/ ?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<!-- <script src="/public/static/jquery.min.js"></script> -->
<script src="/public/static/js/vue.min.js"></script>
<style>
    #app {
        margin: 0 auto;
        width: 90%;
    }

    #input {
        width: 50%;
    }

    #input label {
        width: 100px;
        display: block;
        /* text-align: right; */
        padding-right: 10px;
    }

    #input input {
        height: 30px;
        width: 200px;
        margin-bottom: 10px;
    }
</style>

<body>
    <div id='app'>
        <div id="input">
            <label for="username">用户名</label>
            <input type="username" name="username" placeholder="用户名" v-model="username" autocomplete="off"><br>

            <label for="password">密码</label>
            <input type="password" name="password" value="" placeholder="密码" v-model="password" autocomplete="off"> <br>

            <label for="source">数据来源</label>
            <input type="text" name="source" id="source" placeholder="数据来源" v-model="source">0-广告主 1-第三方数据 3-第三方工具

            <br>
            <label for="date">转化日期</label>
            <input type="text" name="date" placeholder="转化日期" v-model="date">
            <br>
            <label for="conv_type">转化类型</label>
            <input type="text" name="conv_type" placeholder="转化类型" v-model="conv_type">5-表单提交 6-拨打电话 13-在线咨询 14-其他
            15-访客数
            <br>

            <label for="conv_name">转化名称</label>
            <input type="text" name="conv_name" placeholder="转化名称" v-model="conv_name"><br>

            <label for="conv_value">转化值</label>
            <input type="text" name="conv_value" value="" placeholder="转化值" v-model="conv_value"><br>

            <textarea name="urls" id="urls" cols="100" rows="10" v-model="urls" placeholder="转化的url地址，多条之间需要回车换行"
                style="width: 180%;"></textarea>
            <button @click="conv()">转化数据</button>
        </div>

        <form action="/api/wo_long/ocpc" method="post" enctype="application/x-www-form-urlencoded"
            style="position: absolute;right: 20px;top: 20px;width: 50%;">
            <textarea name="datas" id="datas" v-model="conv_data_json" style="width:90%;height:200px"></textarea><br>
            <button type="submit">提交回传</button>
        </form>
    </div>
</body>
<script>
    (function (Vue) {
        var app = new Vue({
            el: '#app',
            data: {
                username: '',
                password: '',
                source: 0,
                date: getNowFormatDate(),
                conv_type: 13,
                conv_name: "",
                conv_value: 1,
                urls: '',
                conv_data_json: ''
            },
            methods: {
                conv: function () {
                    var strs = this.urls.split("\n");
                    var data = new Array();
                    //循环获取url地址中的cilckid参数
                    for (let i = 0; i < strs.length; i++) {
                        var url = strs[i].trim()
                        var matchet = /\?([0-9a-z&=]+)?/
                        if (!matchet.exec(url)) {
                            continue;
                        }
                        var clickid = getQueryString('clickid', url);
                        console.log(clickid);
                        // 获取到的数据添加到数组中
                        if (clickid != null && clickid != undefined && clickid != '') {
                            data.push({
                                "date": this.date,
                                "click_id": clickid,
                                "conv_type": this.conv_type,
                                "conv_name": this.conv_name,
                                "conv_value": this.conv_value
                            });
                        }

                    }
                    // 需要转化的数据
                    var conv_data = {
                        "header": {
                            "username": this.username,
                            "password": this.password
                        },
                        "body": {
                            "source": this.source,
                            "data": data
                        }
                    }
                    this.conv_data_json = JSON.stringify(conv_data);
                }
            }
        });
    })(Vue);

    //获取url中的参数，这样调用：getQueryString("参数名1","url地址");
    function getQueryString(name, url) {
        var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
        var r = url.split('?')[1].match(reg);
        if (r != null) {
            return unescape(r[2]);
        }
        return null;
    }

    // 获取当前日期，并将其格式化为YYYY-MM-DD
    function getNowFormatDate() {
        var date = new Date();
        var seperator1 = "-";
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var strDate = date.getDate();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (strDate >= 0 && strDate <= 9) {
            strDate = "0" + strDate;
        }
        var currentdate = year + seperator1 + month + seperator1 + strDate;
        return currentdate;
    }
</script>

</html>