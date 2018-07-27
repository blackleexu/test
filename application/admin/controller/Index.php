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
        $data=Db::table('user')
        ->alias('a')
        ->join('department d','a.did=d.did')
        ->join('role r','a.rid=r.rid')
        ->field('uid,uname,a.did,a.rid,department,rolename,hobby')
        ->select();
//        $h=$this->convert($data['hobby']);
//        $data['hobby']=$h;
        foreach ($data['hobby'] as $va){

        }
//        dump($data);
//        die();
        $department=Db::table('department')->select();
        $role=Db::table('role')->select();
        $hobby=Db::table('hobby')->select();
        $this->assign('user',$data);
        $this->assign('department',$department);
        $this->assign('role',$role);
        $this->assign('hobby',$hobby);
        return $this->fetch('index/empty');
    }
    public function convert($str){
        $array = explode(",",$str);
        return $array;
    }
    public function edit(){
        $id=input("param.uid");
        $data=User::find($id);
        echo $data->hidden(['create_at','update_at','delete_time','password'])->toJson();
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

        Db::startTrans();
        $re=User::destroy($id);
        if($re){
            Db::commit();
        }else{
            Db::rollback();
        }
        if ($re){
            $date=[
                'status'=>0,
                'msg'=>'删除成功'
            ];
        }else{
            $date=[
                'status'=>1,
                'msg'=>'删除失败'
            ];
        }
        return $date;
    }

    public function userAdd(Request $request){
        //对数据进行过滤，防止未知的用户输入风险（sql注入、恶意输入）
//        $request->filter(['strip_tags','htmlspecialchars']);
        $typename= $request->post('typename');

        //获取数据列数
        $model=new User();
        $num=$model->where("typename='$typename'")->Count();

        if($num>0){
            Session::flash('typeerror',"该类型已存在");
            $this->redirect('admin/index');
        }else{
            $data=["typename"=>$typename];
            if($model->save($data)){
                Session::flash('typesuccess',"添加成功");
                $this->redirect('admin/index');
            }else{
                Session::flash('typeerror',"添加失败");
                $this->redirect('admin/index');
            }
        }
    }
    public function userUpdate(){
        $data=input("param.");
        dump($data);
        die();
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


    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }
    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
