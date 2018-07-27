<?php
namespace app\admin\controller;

use app\admin\model\Teacher;
use think\Controller;
use think\Db;
use think\Session;

class TeacherController extends BaseController
{
    public function index(){
        $teacher=Teacher::all();
        $this->assign('teacher',$teacher);
        return $this->fetch('teacher/index');
    }
    public function edit(){
        $tid=input('param.teacherId');
        $data=Teacher::find($tid);
        echo $data->hidden(['createtime','updatetime'])->toJson();
    }

    public function stuDel(){
        $id=input('param.tid');
        Db::startTrans();
        $re=Teacher::destroy($id);
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

    public function teachUpdate(){
        $data=request()->post();
        $file = request()->file('pic');
        $stu=new Teacher();
        if($file){
            // 先删除原头像
            $pic=Teacher::where('teacherId',$data['teacherId'])->value('pic');
            $filename=ROOT_PATH . 'public' . DS . 'static' . DS . 'img/'.$pic;
            if(file_exists($filename)){
                $delPic=unlink($filename);
                if(!$delPic){
                    Session::flash('teachererror','头像更新失败');
                    $this->redirect('admin/teacher');
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
                Session::flash('teachererror','头像更新失败');
                $this->redirect('admin/teacher');
            }
        }
        $result=$stu->validate(true)->save($data,['teacherId'=>$data['teacherId']]);
        if (false === $result) {
            // 验证失败 输出错误信息
            Session::flash('teachererror',$stu->getError());
            $this->redirect('admin/teacher');
        } else {
            Session::flash('teachersuccess','更新成功');
            $this->redirect('admin/teacher');
        }
    }
}