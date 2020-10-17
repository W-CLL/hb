<?php
/**-------------公众号模块公共函数---------------- */


/**
 * 定义了检查Signature的函数,这是接入微信api成为开发者的第一步，
 * 开发者通过检验signature对请求进行校验，若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，成为开发者成功，否则接入失败。
 */
function checkSignature($token)
{
    // 验证消息的确来自微信服务器
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);

    if ($tmpStr == $signature) {
        return true;
    } else {
        return false;
    }
}

/**
 * 用来检测access_token是否过期，过期会自动获取，
 * 并保存在access_token.json，然后返回一个对象，
 * 对象包含三个属性access_token，expires_in,expire_time
 */
 function checkToken($appid, $secret)
{
    //从文件中加载access_token的相关数据
    $filename = "./wxapi/access_token.php";
    $json = trim(substr(file_get_contents($filename), 15));
    // var_dump($json);
    $tokenInfo = json_decode($json);
    // var_dump($tokenInfo);返回的是一个对象

    // 如果access_token不存在，或者过期了则重新获取
    if (empty($tokenInfo->access_token) || ($tokenInfo->expire_time <= time())) {
        $get_access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        // 调用函数获取access_token
        $result = curlOpen($get_access_token_url, array('ssl' => true));
        if ($result) {
            //先将数据解析为对象，再添加access_token的结束时间记录，然后再保存为json文件
            $tokenInfo = json_decode($result);
            $tokenInfo->expire_time = time() + $tokenInfo->expires_in;
            // var_dump($tokenInfo);
            if (!file_put_contents($filename, "<?php exit();?>" . json_encode($tokenInfo))) {
                // 保存access_token信息到json配置文件失败
                return false;
            }
        } else {
            //请求url失败
            return false;
        }
    }
    return $tokenInfo;
}


    
/**拉取关注公众号的用户信息*/
function pull_userinfo($access_token,$openid)
{
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
    $json = curlOpen($url,array('ssl'=>true));
    file_put_contents('./log/wxapiLog.txt',"$json\r\n", FILE_APPEND);
    $res = json_decode($json,true);
    return $res;
}


/*
 * 生成随机字符串
 * @param int $length 生成随机字符串的长度
 * @param string $char 组成随机字符串的字符串
 * @return string $string 生成的随机字符串
 */
function str_rand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    if(!is_int($length) || $length < 0) {
        return false;
    }

    $string = '';
    for($i = $length; $i > 0; $i--) {
        $string .= $char[mt_rand(0, strlen($char) - 1)];
    }

    return $string;
}
