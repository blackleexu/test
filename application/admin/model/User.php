<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
    use SoftDelete;
    protected $autoWriteTimestamp="datetime";
    protected $table='user';
    protected $pk='uid';
    protected $createTime='create_at';
    protected $updateTime='update_at';
    protected $deleteTime = 'delete_at';

    /**
     *
     * 返回用户信息列表
     * @access public
     * @param string
     * @return array [
     *      ['name'=>'xxx','sex'=>'1']
     * ]
     */
    public static function getUserList(){
        $key = input('param.key','');
        $click_department_key=input('param.click_department_key');
        $click_role_key=input('param.click_role_key');
        $user_search = Db::table('user')
            ->alias('user')
            ->join('department dpment','user.did=dpment.did')
            ->join('role role','user.rid=role.rid')
            ->field('uid,uname,user.did,user.rid,department,rolename,hobby');

        $user_search->whereNull('delete_at');

        if(trim($key)){
            $user_search->where('uname','like','%'.$key.'%');
        }
        if(trim($click_department_key)){
            $user_search->where('department','like','%'.$click_department_key.'%');
        }
        if(trim($click_role_key)!=''){
            $user_search->where('rolename','like','%'.$click_role_key.'%');
        }

//        return $user_search->paginate(3);
        return $user_search->select();
    }

    /**
     * @access public static
     * @return array[
     *      hid=1,
     *      hobby='跑步'
     * ]
     */
    public static function getHobbyList(){
        $hobby_list=Db::table('hobby')->select();
        return $hobby_list;
    }

    /**
     * @access public static
     * @return array[
     *      did=1,
     *      department='PHP_department''
     * ]
     */
    public static function getDepartmentList(){
        $dpment_list=Db::table('department')->select();
        return $dpment_list;
    }

    /**
     * @access public static
     * @return array[
     *      rid=1,
     *      rolename='组长''
     * ]
     */
    public static function getRoleList(){
        $role_list=Db::table('role')->select();
        return $role_list;
    }

    /**
     * @param array  //
     * @param string
     * @param string
     * @param bool
     * @return array
     */
    public static function tree_item_click($data,$str_id,$str_name,$spread)
    {
        $result=[];
        $children=[];
        $result[0]['name']='部门';
        for ($i=0;$i<count($data);$i++){
            if(is_array($data[$i])){
                self::tree_item_click($data[$i],$str_id,$str_name);
            }else{
                $children[$i]['name'] = $data[$i][$str_name];
                $children[$i]['id'] = $data[$i][$str_id];
            }
        }
        $result[0]['children']=$children;
        $result[0]['spread']=$spread;
        return $result;
    }





}