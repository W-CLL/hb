<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
use think\Db;
use think\paginator\driver\Bootstrap; // 加载bootstrap类
use think\paginator\driver\Bootstrap3; // 加载bootstrap3类
//首先在函数顶部引入阿里云短信的命名空间，无需修改，官方sdk自带的命名空间
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
//阿里短信函数，$mobile为手机号码，$data为变量 name跟phone

function sendMsg($mobile, $data)
{

    //这里的路径EXTEND_PATH就是指tp5根目录下的extend目录，系统自带常量。alisms为我们复制api_sdk过来后更改的目录名称
    require_once EXTEND_PATH . 'alisms/vendor/autoload.php';
    Config::load();             //加载区域结点配置

    $accessKeyId = 'LTAI2oAl7WZVaPcI';  //阿里云短信获取的accessKeyId

    $accessKeySecret = 'IYfUqizE4hiR7NDRGaYpfyxgTpLwmL';    //阿里云短信获取的accessKeySecret

    //这个个是审核过的模板内容中的变量赋值，记住数组中字符串code要和模板内容中的保持一致
    //比如我们模板中的内容为：你的验证码为：${code}，该验证码5分钟内有效，请勿泄漏！
    $templateParam = $data;           //模板变量替换

    $signName = '海豹数字科技'; //这个是短信签名，要审核通过

    $templateCode = 'SMS_172886024';   //短信模板ID，记得要审核通过的


    //短信API产品名（短信产品名固定，无需修改）
    $product = "Dysmsapi";
    //短信API产品域名（接口地址固定，无需修改）
    $domain = "dysmsapi.aliyuncs.com";
    //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
    $region = "cn-hangzhou";

    // 初始化用户Profile实例
    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
    // 增加服务结点
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    // 初始化AcsClient用于发起请求
    $acsClient = new DefaultAcsClient($profile);

    // 初始化SendSmsRequest实例用于设置发送短信的参数
    $request = new SendSmsRequest();
    // 必填，设置雉短信接收号码
    $request->setPhoneNumbers($mobile);

    // 必填，设置签名名称
    $request->setSignName($signName);

    // 必填，设置模板CODE
    $request->setTemplateCode($templateCode);

    // 可选，设置模板参数
    if ($templateParam) {
        $request->setTemplateParam(json_encode($templateParam));
    }

    //发起访问请求
    $acsResponse = $acsClient->getAcsResponse($request);

    //返回请求结果
    $result = json_decode(json_encode($acsResponse), true);
    return $result;
}

function getIp()
{
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    return $res;
}
function setValue($list)
{
    $Auth = Db::table('user_auth')
        ->where('Auth_Id', '>', session('auth'))
        ->select();
    $Type = Db::table('user_type')
        ->where('Type_Id', '>', session('type'))
        ->select();
    // 转换数组的值
    if (count($list) == count($list, 1)) {
        //如果是一维数组
        if ($list['Status'] == 1) {
            $list['NStatus'] = "正常";
        } elseif ($list['Status'] == 2) {
            $list['NStatus'] = "隐藏";
        } else {
            $list['NStatus'] = "冻结";
        }
        foreach ($Auth as $k => $v) {
            if ($list['Auth_Id'] == $v['Auth_Id']) {
                $list['Auth_Name'] = $v['Auth_Name'];
            }
        }
        foreach ($Type as $k => $v) {
            if ($list['Type_Id'] == $v['Type_Id']) {
                $list['Type_Name'] = $v['Type_Name'];
            }
        }
        $list['Login_time'] = date('Y-m-d H:m:s', $list['Login_time']);
        return $list;
    } else {
        //如果是多维数组
        foreach ($list as $a => $b) {

            foreach ($b as $n => $d) {
                if ($d == "" || $d == null) {
                    $list[$a][$n] = "空";
                }
            }
            foreach ($Auth as $k => $v) {
                if ($b['Auth_Id'] == $v['Auth_Id']) {
                    $list[$a]['Auth_Name'] = $v['Auth_Name'];
                }
            }
            foreach ($Type as $k => $v) {
                if ($b['Type_Id'] == $v['Type_Id']) {
                    $list[$a]['Type_Name'] = $v['Type_Name'];
                }
            }
            if ($b['Status'] == 1) {
                $list[$a]['NStatus'] = "正常";
            } elseif ($b['Status'] == 2) {
                $list[$a]['NStatus'] = "隐藏";
            } else {
                $list[$a]['NStatus'] = "冻结";
            }
            $list[$a]['Login_time'] = date('Y-m-d H:m:s', $list[$a]['Login_time']);
        }

        return $list;
    }
}
//把join连接为null的值转换为无
function setNull($list)
{
    foreach ($list as $k => $v) {
        foreach ($v as $k2 => $v2) {
            if ($v2 == null) {
                $list[$k][$k2] = '无';
            }
        }
    }
    return $list;
}

function Pro_User_Id()
{
    //如果不是管理员以上权限，只能看到自己所管理的项目推广账号
    $P_Id = Db::table('project')
        ->where('Status', 1)
        ->where(function ($query) {
            $query->where('See_User_Id', 'like', '%@,' . session('id') . '@,%')
                ->whereOr('User_Id', session('id'));
        })
        ->field('Pro_User_Id')
        ->select();
    //正则表达式
    $pattern = "/[@,]+/";
    $Pro_User_Id = [];
    foreach ($P_Id as $k => $v) {
        //匹配标识符
        if (preg_match($pattern, $v['Pro_User_Id'])) {
            //如果多个就存起来
            $arr = explode("@,", $v['Pro_User_Id']);
            foreach ($arr as $k => $v) {
                $Pro_User_Id[] = $v;
            }
        } else {
            //如果一个直接返回
            $Pro_User_Id[] = $v['Pro_User_Id'];
        }
    }
    //数组去重复
    $Pro_User_Id = array_unique($Pro_User_Id);
    return $Pro_User_Id;
}
function User_53_Id()
{
    //如果不是管理员以上权限，只能看到自己所管理的53账号
    $P_Id = Db::table('project')
        ->where('User_Id', session('id'))
        ->whereOr('See_User_Id', 'like', '%@,' . session('id') . '@,%')
        ->field('User_53_Id')
        ->select();
    //正则表达式
    $pattern = "/[@,]+/";
    $User_53_Id = [];
    foreach ($P_Id as $k => $v) {
        //匹配标识符
        if (preg_match($pattern, $v['User_53_Id'])) {
            //如果多个就存起来
            $arr = explode("@,", $v['User_53_Id']);
            foreach ($arr as $k => $v) {
                $User_53_Id[] = $v;
            }
        } else {
            //如果一个直接返回
            $User_53_Id[] = $v['User_53_Id'];
        }
    }
    //数组去重复
    $User_53_Id = array_unique($User_53_Id);
    return $User_53_Id;
}
function getKeyword($url)
{
    //将来如果扩展可以再传一个标识的字符串如:（$url,$str）
    if ($url == "") return null;
    //url解码
    $url = urldecode($url);
    //分割？后面的字符串并转换成大写
    $search = strtoupper(substr($url, strpos($url, "?") + 1));
    //分割标识
    $res = explode('BBBB', $search);
    //去除掉标识符前面的字符串
    unset($res[0]);
    //数组拼接
    return implode('', $res);
}
function getKeyword2($i_url)
{

    $url = urldecode(strtolower($i_url));
    file_put_contents("getkw.txt", $url . "\r\n", FILE_APPEND);
    $i_search = array( //各对应的搜索引擎
        "手机百度m"  => ["m.baidu.com", "word|wd"],
        "手机搜狗wap"  => ["wap.sogou.com", "keyword"],
        "手机搜狗m"  => ["m.sogou.com", "keyword"],
        "手机360m"   => ["m.so.com", "q"],
        "手机神马m"      => ["m.sm.cn", "q"],
        "手机神马so.m"      => ["so.m.sm.cn", "q"],
        "手机神马yz.m"      => ["yz.m.sm.cn", "q"],
        "手机头条m"     => ['m.toutiao.com', "keyword"],
        "百度"      => ["www.baidu.com", "word|wd"],
        "搜狗"      => ["www.sogou.com", "query"],
        "360"       => ["www.so.com", "q"],
        "必应"      =>  ["cn.bing.com", "q"]
    );
    $url = parse_url($url);
    //url如果不严谨就返回false

    if (!$url) return ['search' => '', 'kw' => ''];
    $hostname = $url['host']; //域名

    $query = $url['query']; //url参数

    $re_value['search'] = null; //返回值["search"=>"搜索引擎","keyword"=>"关键词"]
    $re_value['keyword'] = null;
    //化为数组 获取参数
    $query = explode("&", $query);

    //判断搜索引擎，获取关键词
    foreach ($i_search as $k => $v) {
        //dump($hostname);
        // dump($v[0]);
        if ($hostname == $v[0]) {

            $re_value["search"] = $k;
            //匹配搜索关键字
            foreach ($query as $qk => $qv) {
                if (preg_match('/(' . $v[1] . ')=/', $qv, $match)) {
                    // /(word|wd)=/,"wd='dsa'",
                    $re_value["kw"] = str_replace($match[0], '', $qv);
                    break;
                }
            }
            break;
        }
    }
    return $re_value;
}

/**
 * 发送请求的函数，成功返回请求内容，失败返回false
 * 使用：  
 * echo curlOpen('https://www.baidu.com');  
 *  
 * POST数据  
 * $post = array('aa'=>'ddd','ee'=>'d')  
 * 或  
 * $post = 'aa=ddd&ee=d';  
 * echo curlOpen('https://www.baidu.com',array('post'=>$post));  
 * @param string $url
 * @param array $config
 */
function curlOpen($url, $config = array())
{
    $arr = array(
        'post' => false, 'referer' => $url, 'cookie' => '',
        'useragent' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; customie8)',
        'timeout' => 20, 'return' => true, 'proxy' => '', 'userpwd' => '', 'nobody' => false,
        'header' => array(), 'gzip' => true, 'ssl' => false, 'isupfile' => false
    );
    $arr = array_merge($arr, $config);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $arr['return']);
    curl_setopt($ch, CURLOPT_NOBODY, $arr['nobody']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $arr['useragent']);
    curl_setopt($ch, CURLOPT_REFERER, $arr['referer']);
    curl_setopt($ch, CURLOPT_TIMEOUT, $arr['timeout']);
    //curl_setopt($ch, CURLOPT_HEADER, true);//获取header
    if ($arr['gzip']) curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    if ($arr['ssl']) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (!empty($arr['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $arr['cookie']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $arr['cookie']);
    }
    if (!empty($arr['proxy'])) {
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); 
        curl_setopt($ch, CURLOPT_PROXY, $arr['proxy']);
        if (!empty($arr['userpwd'])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $arr['userpwd']);
        }
    }
    //ip比较特殊，用键值表示
    if (!empty($arr['header']['ip'])) {
        array_push($arr['header'], 'X-FORWARDED-FOR:' . $arr['header']['ip'], 'CLIENT-IP:' . $arr['header']['ip']);
        unset($arr['header']['ip']);
    }
    $arr['header'] = array_filter($arr['header']);
    if (!empty($arr['header'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arr['header']);
    }
    if ($arr['post'] != false) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($arr['post']) && $arr['isupfile'] === false) {
            $post = http_build_query($arr['post']);
        } else {
            $post = $arr['post'];
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $result = curl_exec($ch);
    //var_dump(curl_getinfo($ch));
    curl_close($ch);
    return $result;
}
/**
 * 查询电话号码归属地
 */
function getGuiShuDi($phone_number)
{
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
    header('Access-Control-Allow-Methods: GET, PUT, POST');
    $name = null;
    $input = null;
    // https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?cb=jQuery11020625057433439415_1586403472219&resource_name=guishudi&query=13789855658&_=1586403472226
    // https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?cb=jQuery11020043465807566894554_1586405897758&resource_name=guishudi&query=17879529508&_=1586405897759
    $urls = [
        //百度手机卫士，精确到地市
        "sp0.baidu" => "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?resource_name=guishudi&query=" . $phone_number,
        //百度，精确到地市
        //http://mobsec-dianhua.baidu.com/dianhua_api/open/location?tel=". $phone_number
        //淘宝，精确到省份
        "taobao" => "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=" . $phone_number
        //
        // 拍拍 参数：  mobile：手机号码  callname：回调函数   amount：未知（必须）
        // http://virtual.paipai.com/extinfo/GetMobileProductInfo?mobile=手机号码&amount=10000&callname=getPhoneNumInfoExtCallback
        //
        // 财付通  参数：chgmobile：手机号码 返回：xml
        // API地址： http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile=手机号码
        //
        // 百付宝   参数：phone：手机号码   callback：回调函数   cmd：未知（必须）
        // API地址： https://www.baifubao.com/callback?cmd=1059&callback=phone&phone=手机号码
        // 
        // 115  参数：mobile：手机号码  callback：回调函数
        // API地址： http://cz.115.com/?ct=index&ac=get_mobile_local&callback=jsonp1333962541001&mobile=手机号码
    ];
    //调用api，如果查询到电话归属地信息就跳出循环
    foreach ($urls as $k => $v) {
        $model = curlOpen($v, array('ssl' => true));
        if ($model) {
            $name = $k;
            $input = $model;
            break;
        }
    }
    //字符串编码转换
    $input = iconv('GBK', 'UTF-8', $input);
    // 解析数据
    if ($name == "sp0.baidu") {
        //preg_match_all（“正则表达式”,"截取的字符串","成功之后返回的结果集（是数组）"）
        $d = json_decode($input, true);
        $city = $d['data'][0]['city'];
        $prov = $d['data'][0]['prov'];
        $type = $d['data'][0]['type'];
        return $prov . ' ' . $city . ' ' . $type . ' ';
    } elseif ($name == 'taobao') {
        $d = preg_replace("/__GetZoneResult_ = /", "", $input);
        if ($d) {
            return $d;
        } else {
            return $input;
        }
    } else {
        return '未知归属地';
    }
}
/**
 * 传入url地址解析得到参数数组
 */
function getParams($url)
{
    $arr = parse_url($url);
    $query = $arr['query'];
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}
