<?php
namespace app\project\validate;

use think\Validate;

class Project extends Validate
{
    protected $rule = [
        'EstimatedCost'  =>  'number',
        'CustomerBudget' =>  'number',
    ];

}