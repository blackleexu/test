<?php
namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Hobby;
use app\admin\model\Login;
use app\admin\model\Department;
use app\admin\model\Role;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class Index extends BaseController
{
    public function index(){
        $data  = User::getUserList();
        //爱好表查询
        $hobby=Hobby::all();
        //根据爱好id获取对应的爱好名
        Hobby::hobby_convert($data,$hobby);
        //部门查询
        $department=User::getDepartmentList();
        //角色查询
        $role=Role::all();

        $this->assign('user',$data);
        $this->assign('department',$department);
        $this->assign('role',$role);
        $this->assign('hobby',$hobby);
        return $this->fetch('index/empty');
    }

    public function edit(){
        $id=input("param.uid");
        $data=User::find($id);
        $data['count']=Db::table('hobby')->count();
        echo $data->hidden(['create_at','update_at','delete_at','password'])->toJson();
    }

    public function dpChange(){
        $uid=input('param.uid');
        $did=input('param.did');
        $data=User::get($uid);
        $data->did=$did;
        if($data->save()){
            $result=[
                "status"=>1,
                "msg"=>"修改成功"
            ];
        }else{
            $result=[
                "status"=>0,
                "msg"=>"修改失败"
            ];
        }
        return $result;
    }

    public function roleChange(){
        $uid=input('param.uid');
        $rid=input('param.rid');
        $data=User::get($uid);
        $data->rid=$rid;
        if($data->save()){
            $result=[
                "status"=>1,
                "msg"=>"修改成功"
            ];
        }else{
            $result=[
                "status"=>0,
                "msg"=>"修改失败"
            ];
        }
        return $result;
    }

    public function userDel(){
        $id=input('param.uid');
        $re=User::destroy(['uid'=>$id]);
        if ($re){
            $data=[
                'status'=>0,
                'msg'=>'删除成功'
            ];
        }else{
            $data=[
                'status'=>1,
                'msg'=>'删除失败'
            ];
        }
        return $data;
    }

    public function userAdd(Request $request){
        $data= $request->post();
        $data['password']=md5($data['password']);

        $str='';
        for($i=0;$i<count($data['hobby']);$i++){
            $str.=$data['hobby'][$i].",";
        }
        $data['hobby']=substr($str,0,strlen($str)-1);

        //获取数据列数
        $model=new User();
        $result=$model->validate(true)->save($data);

        if($result){
            Session::flash('usersuccess',"添加成功");
            $this->redirect('admin/index');
        }else{
            Session::flash('usererror',$model->getError());
            $this->redirect('admin/index');
        }
    }

    public function userUpdate(){
        $data=input("param.");

        $data['hobby']=Hobby::hobby_format_before_update($data);

        $user=new User();
        $result=$user->validate(true)->save($data,['uid'=>$data['uid']]);
        if(!$result){
            Session::flash('usererror',$user->getError());
            $this->redirect('admin/index');
        }else{
            Session::flash('usersuccess',"修改成功");
            $this->redirect('admin/index');
        }
    }

    public function getDepartmentList(){
        $department_list_arr=Department::all();
        return User::tree_item_click($department_list_arr,'did','department',true);
    }

    public function getRoleList(){
        $role_list_arr=Role::all();
        return User::tree_item_click($role_list_arr,'rid','rolename',true);
    }

    public function getItemByKey(){
        $data  = (new User())->getUserList();
        //爱好表查询
        $hobby=Hobby::all();
        //用户表爱好id转对应的爱好名
        Hobby::hobby_convert($data,$hobby);
        return $data;
    }
}
