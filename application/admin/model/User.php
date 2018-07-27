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
//    protected $defaultSoftDelete = 0;

    /**
     * ssssss
     * @return array [
     *      ['name'=>'xxx','sex'=>'1']
     * ]
     */
    public function getUserList(){
        $key = input('param.key','');

        $user_search = Db::table('user')
            ->alias('a')
            ->join('department d','a.did=d.did')
            ->join('role r','a.rid=r.rid')
            ->field('uid,uname,a.did,a.rid,department,rolename,hobby');

        $user_search->whereNull('delete_at');

        if(trim($key)){
            $user_search->where('uname','like','%'.$key.'%');
        }

        return $user_search->select();
    }

}