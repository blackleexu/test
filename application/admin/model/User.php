<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
    use SoftDelete;
    protected $dateFormat="datetime";
    protected $table='user';
    protected $pk='uid';
    protected $createTime='create_at';
    protected $updateTime='update_at';
    protected $deleteTime = 'delete_time';
}