<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Hobby extends Model
{
    protected $table='hobby';
    protected $pk='did';

    /**
     * @access public
     * @param array
     * @param $hobby
     * return 无返回
     */
    public static function hobby_convert(&$data,&$hobby){
        if(is_array($data)&&is_array($hobby)){
            for ($i=0;$i<count($data);$i++){
                $tmp=explode(",",$data[$i]['hobby']);
                $data[$i]['hobby']='';
                for ($j=0;$j<count($tmp);$j++){
                    $data[$i]['hobby'].=$hobby[intval($tmp[$j])-1]['hobby'].',';
                }
                $data[$i]['hobby']=substr($data[$i]['hobby'],0,strlen($data[$i]['hobby'])-1);
            }
        }
    }

    /**
     * @param array
     * @return bool|string
     */
    public static function hobby_format_before_update($data){
        $str='';
        for($i=0;$i<count($data['hobby']);$i++){
            $str.=$data['hobby'][$i].",";
        }
        return substr($str,0,strlen($str)-1);
    }

}