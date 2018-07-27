<?php

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    protected $rule=[
        'uname'=>'require|max:12|unique:user,uname',
    ];
    protected $message=[
        'uname.require'=>'未填写用户名',
        'uname.max'=>'用户名长度不能超过12',
        'uname.unique'=>'用户名已存在',
    ];
}
