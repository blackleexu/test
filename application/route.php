<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::group('admin',function () {
    Route::bind('admin');
    Route::any('login', 'admin/LoginController/login');
    Route::any('logout', 'LoginController/logout');
//});
//视频类型
    Route::any('index', 'Index/index');
    Route::any('edit', 'Index/edit');
    Route::any('userdel', 'Index/userDel');
    Route::any('useradd', 'Index/useradd');
    Route::any('userupdate', 'Index/userUpdate');
    Route::any('dpchange', 'Index/dpChange');
    Route::any('rolechange', 'Index/roleChange');
});

