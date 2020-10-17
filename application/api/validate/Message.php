<?php

namespace app\api\validate;

use think\Validate;

class Message extends Validate
{
    protected $rule = [
        'Phone|电话号码' => 'require|max:11|number',
        'Name|姓名' => 'require'
    ];
}
