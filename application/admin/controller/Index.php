<?php
namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Hobby;
use app\admin\model\Department;
use app\admin\model\Role;
use think\Db;
use think\Request;
use think\Session;

class Index extends BaseController{
    public function index(){
        $paginator  = User::getUserList();
        //爱好表查询
        $hobby =  Hobby::all();
        $data_list = Hobby::hobby_convert($paginator,$hobby);
        //部门查询
        $department = User::getDepartmentList();
        //角色查询
        $role = Role::all();

        $this->assign('data_list',$data_list);
        $this->assign('paginator',$paginator);
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
        if(isset($data['hobby'])){
            $data['hobby']=Hobby::hobby_format_before_update($data);
        }

        //获取数据列数
        $model=new User();
        $result=$model->validate(true)->save($data);
        if(!$result){
            return $update_result=[
                'status'=>0,
                'msg'=>'修改失败'
            ];
        }else{
            $this->redirect('admin/index');
        }
    }

    public function userUpdate(){
        $data=input("param.");

        $data['hobby']=Hobby::hobby_format_before_update($data);
        $user=new User();
        $result=$user->validate(true)->save($data,['uid'=>$data['uid']]);
        if(!$result){
            return $update_result=[
                'status'=>0,
                'msg'=>'修改失败'
            ];
        }else{
            $this->redirect('admin/index');
        }
    }

    public function getDepartmentList(){
        $department_list_arr=Department::all();
        return User::tree_item_click($department_list_arr,'did','department',true,'部门');
    }

    public function getRoleList(){
        $role_list_arr=Role::all();
        return User::tree_item_click($role_list_arr,'rid','rolename',true,'职位');
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
