<?php 

namespace app\wxapi\model;

use think\Model;

class Gzh extends Model
{
    public function get_user()
    {
        $data = $this->table('wx_user')->alias('a')->join('user_info b','a.User_Id=b.User_Id')->select();
        return $data;
    }
}