<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Role extends Model
{
    protected $table='role';
    protected $pk='did';

}