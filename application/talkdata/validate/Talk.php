<?php

namespace app\talkdata\validate;

use think\Validate;

class Talk extends Validate
{
    protected $rule = [
        'Id' => 'require|number',
        'Cate_Name|分类名称' => 'require|max:50|unique:talk_cate',
        'Talk_Type|谈话类型' => 'require|in:g,u',
        'Talk_Content|谈话内容' => 'require',
        'Create_time|时间' => 'require|number',
        'Category_Id|标题分类' => 'require|number',
    ];

    protected $scene = [
        'add_cate' => ['Cate_Name'],
        'edit_cate' => ['Id', 'Cate_Name'],
        'upd_talk' => ['Id','Talk_Type','Talk_Content','Create_time','Category_Id'],
    ];
}
