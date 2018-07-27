<?php
namespace app\admin\controller;

use app\admin\model\Match;
use app\admin\model\Type;
use think\Db;
use think\Loader;
use think\Session;

class LessonController extends BaseController
{
    public function index(){
        $model=new Match();
        $subsql = Db::table('teacher')->field('pic',true)->group('teacherId')->buildSql();
        $lesson=Db::table('lesson')
            ->alias('a')
            ->join('type b','a.tid = b.tid')
            ->join([$subsql=>'c'],'a.teacherid = c.teacherid')
            ->select();
        $type=Type::all();
        $this->assign('type',$type);
        $this->assign('lesson',$lesson);
//        dump($lesson);
        return $this->fetch('lesson/index');
    }

    public function edit(){
        $lid=input("param.lessonid");
        $type=Match::find($lid);
//        echo json_encode($type);
        echo $type->hidden(['createtime','updatetime'])->toJson();
    }

    public function lessondel(){
        $id=input('param.lessonid');
//        $re=Type::where('tid',$id)->delete();
        Db::startTrans();
        $re=Match::destroy($id);
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
    public function lessonUpdate(){
        $data=request()->post();
        $file = request()->file('pic');
        $lesson=new Match;
        if($file){
            // 先删除原头像
            $pic=Match::where('lessonid',$data['lessonid'])->value('pic');
            $filename=ROOT_PATH . 'public' . DS . 'static' . DS . 'poster/'.$pic;
            if(unlink($filename)) {
                //上传新头像
                $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'poster');
                if ($info) {
                    $pic = $info->getFilename();
                    $data['pic'] = $pic;
                }
            }else{
                Session::flash('lessonerror','头像更新失败');
                $this->redirect('admin/lesson');
            }
        }
        $result=$lesson->validate(true)->save($data,['lessonid'=>$data['lessonid']]);
        if (false === $result) {
            // 验证失败 输出错误信息
            Session::flash('lessonerror',$lesson->getError());
            $this->redirect('admin/lesson');
        } else {
            Session::flash('lessonsuccess','更新成功');
            $this->redirect('admin/lesson');
        }
    }
}