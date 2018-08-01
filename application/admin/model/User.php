<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Session;
use traits\model\SoftDelete;

class User extends Model{
    use SoftDelete;
    protected $autoWriteTimestamp="datetime";
    protected $table='user';
    protected $pk='uid';
    protected $createTime='create_at';
    protected $updateTime='update_at';
    protected $deleteTime = 'delete_at';

//    public static $key='';

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

        $query = array_merge($_GET,$_POST);

        if(trim($key)){
            $user_search->where('uname','like','%'.$key.'%');
        }
        if(trim($click_department_key)!=''){
            $user_search->where('department','like','%'.$click_department_key.'%');
        }
        if(trim($click_role_key)!=''){
            $user_search->where('rolename','like','%'.$click_role_key.'%');
        }

        return $user_search->paginate(8,false,[
            'query' => $query
        ]);
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
     * @param $data array
     * @param $str_id 设置为id的字段
     * @param $str_name 设置为text的字段
     * @param $spread 是否处于打开状态
     * @param $tab_name 当前root标签的名字
     * @return array
     */
//    public static function tree_item_click($data,$str_id,$str_name,$spread,$tab_name){
//        $result=[];
//        $children=[];
//        $result[0]['name']=$tab_name;
//        for ($i=0;$i<count($data);$i++){
//            if(is_array($data[$i])){
//                self::tree_item_click($data[$i],$str_id,$str_name,$tab_name);
//            }else{
//                $children[$i]['text'] = $data[$i][$str_name];
//                $children[$i]['id'] = $data[$i][$str_id];
//            }
//        }
//        $result[0]['children']=$children;
//        $result[0]['spread']=$spread;
//        return $result;
//    }
    public static function tree_item_click($data,$str_id,$str_name,$spread,$tab_name){
//        [
//            { "id" : "ajson1", "parent" : "#", "text" : "Simple root node" },
//            { "id" : "ajson2", "parent" : "#", "text" : "Root node 2" },
//            { "id" : "ajson3", "parent" : "ajson1", "text" : "Child 1" },
//            { "id" : "ajson4", "parent" : "ajson2", "text" : "Child 2" },
//        ]
        $length=count($data);
        $state=['opened'=>true];
        $result=[];
        $children=[];
        $result[0]=[ "id" => 'root', "parent" => "#", "text" => '用户', 'state'=>$state];
        $result[1]=[ "id" => $tab_name, "parent" => "root", "text" => $tab_name ,'state'=>$state];
        for ($i=0;$i<$length;$i++){
            $result[$i+2]['text'] = $data[$i][$str_name];
            $result[$i+2]['id'] = $data[$i][$str_id];
            $result[$i+2]['parent'] = $tab_name;
        }
        $result[0]['children']=$children;
        return $result;
    }
}