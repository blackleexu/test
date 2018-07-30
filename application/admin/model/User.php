<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class User extends Model{
    use SoftDelete;
    protected $autoWriteTimestamp="datetime";
    protected $table='user';
    protected $pk='uid';
    protected $createTime='create_at';
    protected $updateTime='update_at';
    protected $deleteTime = 'delete_at';

    /**
     * @return \think\Paginator
     * @throws \think\exception\DbException
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
            return $user_search->paginate(5,false,['query'=>['key'=>$key]]);
        }
        if(trim($click_department_key)!=''){
            $user_search->where('department','like','%'.$click_department_key.'%');
            return $user_search->paginate(5,false,['query'=>['click_department_key'=>$click_department_key]]);
        }
        if(trim($click_role_key)!=''){
            $user_search->where('rolename','like','%'.$click_role_key.'%');
            return $user_search->paginate(5,false,['query'=>['click_role_key'=>$click_role_key]]);
        }

        return $user_search->paginate(5);
    }

    /**
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getHobbyList(){
        $hobby_list=Db::table('hobby')->select();
        return $hobby_list;
    }

    /**
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getDepartmentList(){
        $dpment_list=Db::table('department')->select();
        return $dpment_list;
    }

    /**
     * @access public static
     * @return array
     */
    public static function getRoleList(){
        $role_list=Db::table('role')->select();
        return $role_list;
    }

    /**
     * @param array
     * @param string
     * @param string
     * @param bool
     * @return array
     */
    public static function tree_item_click($data,$str_id,$str_name,$spread,$tab_name){
        $result=[];
        $children=[];
        $result[0]['name']=$tab_name;
        for ($i=0;$i<count($data);$i++){
            if(is_array($data[$i])){
                self::tree_item_click($data[$i],$str_id,$str_name,$tab_name);
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