<?php
namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Login;
use app\admin\model\Type;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class Index extends BaseController
{
    public function index(){
        $data  = (new User())->getUserList();
        //爱好表查询
        $hobby=Db::table('hobby')->select();
        //用户表爱好id转对应的爱好名
        for ($i=0;$i<count($data);$i++){
            $tmp=explode(",",$data[$i]['hobby']);
            $data[$i]['hobby']='';
            for ($j=0;$j<count($tmp);$j++){
//                $data[$i]['hobby'].=$hobby[intval($data[$i]['hobby'][$j])];
                $data[$i]['hobby'].=$hobby[intval($tmp[$j])-1]['hobby'].',';
            }
            $data[$i]['hobby']=substr($data[$i]['hobby'],0,strlen($data[$i]['hobby'])-1);
        }

        $department=Db::table('department')->select();
        $role=Db::table('role')->select();

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
        $str='';
        for($i=0;$i<count($data['hobby']);$i++){
            $str.=$data['hobby'][$i].",";
        }
        $data['hobby']=substr($str,0,strlen($str)-1);
//        dump($data);
//        die();
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
        $result=[];
        $children=[];
        $department_list_arr=Db::table('department')->select();

        $result[0]['name']='部门';
        for ($i=0;$i<count($department_list_arr);$i++){
            $children[$i]['name'] = $department_list_arr[$i]['department'];
            $children[$i]['id'] = $department_list_arr[$i]['did'];
        }
        $result[0]['children']=$children;
        $result[0]['spread']=true;
        return $result;
//        return json_encode($result);
    }

    public function getRoleList(){
        $result=[];
        $children=[];
        $role_list_arr=Db::table('role')->select();

        $result[0]['name']='职位';
        for ($i=0;$i<count($role_list_arr);$i++){
            $children[$i]['name'] = $role_list_arr[$i]['rolename'];
            $children[$i]['id'] = $role_list_arr[$i]['rid'];
        }
        $result[0]['children']=$children;
        return $result;
    }

    public function array_to_json(array $arr){
        $result=[];
        for ($i=0;$i<count($arr);$i++){
            $result[$i]['name'] = $arr[$i]['department'];
            $result[$i]['id'] = $arr[$i]['did'];
        }
        return json_encode($result);
    }

    public function getItemByKey(){
        $data  = (new User())->getUserList();
        //爱好表查询
        $hobby=Db::table('hobby')->select();
        //用户表爱好id转对应的爱好名
        for ($i=0;$i<count($data);$i++){
            $tmp=explode(",",$data[$i]['hobby']);
            $data[$i]['hobby']='';
            for ($j=0;$j<count($tmp);$j++){
//                $data[$i]['hobby'].=$hobby[intval($data[$i]['hobby'][$j])];
                $data[$i]['hobby'].=$hobby[intval($tmp[$j])-1]['hobby'].',';
            }
            $data[$i]['hobby']=substr($data[$i]['hobby'],0,strlen($data[$i]['hobby'])-1);
        }
        return $data;
    }
}
