<?php
return [
    'api_auth'=>false,
    'default_filter' => '',#htmlspecialchars把字符串转换为html实体，trim去两边空格

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------
    // PATHINFO变量名 用于兼容模式
    'var_pathinfo' => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch' => [
        'ORIG_PATH_INFO',
        'REDIRECT_PATH_INFO',
        'REDIRECT_URL'
    ],
];