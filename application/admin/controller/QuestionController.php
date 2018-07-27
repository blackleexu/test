<?php
namespace app\admin\controller;

use app\admin\model\Question;
use think\Db;

class QuestionController extends BaseController
{
    //显示资源列表
    public function index(){
        $question=Question::all();
        $subsql = Db::table('student')->field('sid,studentname')->group('sid')->buildSql();
        $subsql1 = Db::table('lesson')->field('lessonid,lessonname,pic')->group('lessonid')->buildSql();
        $subsql2 = Db::table('videos')->field('vid,vname')->group('vid')->buildSql();
        $question=Db::table('question')
            ->alias('a')
            ->join([$subsql1=>'b'],'a.lessonid = b.lessonid')
            ->join([$subsql=>'c'],'a.studentid = c.sid')
            ->join([$subsql2=>'d'],'a.vid = d.vid')
            ->select();
        $this->assign('question',$question);
//        dump($question);
        return $this->fetch('question/index');
    }
    //删除
    public function questionDel(){
        $id=input('param.qid');
        Db::startTrans();
        $re=Question::destroy($id);
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
}