<?php
namespace app\admin\controller;

use app\admin\model\Student;
use think\Db;
use think\Loader;
use think\Session;

class StudentController extends BaseController
{
    public function index(){
        $student=Student::all();
        $this->assign('student',$student);
        return $this->fetch('student/index');
    }

    public function edit(){
        $sid=input('param.sid');
        $data=Student::find($sid);
        echo $data->hidden(['createtime','updatetime'])->toJson();
    }

    public function stuDel(){
        $id=input('param.sid');
        Db::startTrans();
        $re=Student::destroy($id);
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

    public function stUpdate(){
        $data=request()->post();
        $file = request()->file('pic');
        $stu=new Student();
//        dump($file);
//        dump($data);
//        die();
        if($file){
            // 先删除原头像
            $pic=Student::where('sid',$data['sid'])->value('pic');
            $filename=ROOT_PATH . 'public' . DS . 'static' . DS . 'img/'.$pic;
            if(file_exists($filename)){
                $delPic=unlink($filename);
                if(!$delPic){
                    Session::flash('studenterror','头像更新失败');
                    $this->redirect('admin/student');
                }
            }
            //上传新头像
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'img');
            if ($info) {
                $pic = $info->getFilename();
                $data['pic'] = $pic;
//                dump($data);
//                exit();
            }
            else{
                Session::flash('studenterror','头像更新失败');
                $this->redirect('admin/student');
            }

        }
        $result=$stu->validate(true)->save($data,['sid'=>$data['sid']]);
        if (false === $result) {
            // 验证失败 输出错误信息
            Session::flash('studenterror',$stu->getError());
            $this->redirect('admin/student');
        } else {
            Session::flash('studentsuccess','更新成功');
            $this->redirect('admin/student');
        }
    }

    public function genderChange(){
        $sid=input('param.sid');

        $gender=Student::field('gender')->find($sid);
        //经过测试：
        //虽然数据库中存的是0或者1，但是通过非运算得到的true或false值会自动隐式提升到int类型的0或者1；
        $gender['gender']=!$gender['gender'];
        $data=['sid'=>$sid,'gender'=>$gender['gender']];
        $result=Student::update($data);
        echo $gender->toJson();
    }

}