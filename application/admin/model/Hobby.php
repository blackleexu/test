<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Paginator;
use traits\model\SoftDelete;

class Hobby extends Model{
    protected $table='hobby';
    protected $pk='did';
    /**
     * @param Paginator $paginator
     * @param array $hobby
     * @return array
     */
    public static function hobby_convert($paginator,$hobby){
        $hobby_key_arr = [];
        foreach ($hobby as $h){
            $hobby_key_arr[$h->hid] = $h->hobby;
        }
//        array (size=5)
//          1 => string '跑步'
        $items = $paginator->items();
        foreach ($items as $k => $v){
            $hid_arr = $v['hobby']?explode(",",$v['hobby']):[];//$hid_arr[1,2,3...];
            $hobby_name_arr = [];
            foreach ($hid_arr as $hid){
                if(isset($hobby_key_arr[$hid])){
                    $hobby_name_arr[] = $hobby_key_arr[$hid];
                }
            }
            $items[$k]['hobby'] = implode(',',$hobby_name_arr);
        }
        return $items;
    }
    /**
     * @param array
     * @return bool|string
     */
    public static function hobby_format_before_update($data){
        return implode(',',$data['hobby']);
    }
}