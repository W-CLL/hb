<?php
namespace app\project\model;

use think\Model;
class User extends Model
{
public function project(){
    return $this->belongsTo('Project','User_Id');
}
public function business(){
    return $this->hasOne('User')->select();
}
}