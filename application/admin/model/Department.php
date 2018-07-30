<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Department extends Model
{
    protected $table='department';
    protected $pk='did';

}