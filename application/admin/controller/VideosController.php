<?php
namespace app\admin\controller;

use app\admin\model\Videos;
use think\Db;
use think\Session;

class VideosController extends BaseController
{
    public function index()
    {
        $subsql=Db::table('lesson')->field('lessonid,lessonname')->group('lessonid')->buildSql();
        $video=Db::table('videos')
            ->alias('a')
            ->join([$subsql=>'b'],'a.lessonid = b.lessonid')
            ->select();
        $this->assign('video',$video);
//        dump($video);
        return $this->fetch('video/index');
    }

    public function vEdit(){
        $vid=input('param.vid');
        $data=Videos::find($vid);
//        dump($data);
        echo $data->hidden(['createtime','updatetime'])->toJson();
    }

    public function vDel(){
        $id=input('param.vid');
        Db::startTrans();
        $re=Videos::destroy($id);
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

    public function vUpdate(){
        $data=request()->post();
//        dump($data);
        $video=new Videos();
        $result=$video->save($data,['vid'=>$data['vid']]);
        if (false === $result) {
            // 验证失败 输出错误信息
            Session::flash('videoerror',$video->getError());
            $this->redirect('admin/video');
        } else {
            Session::flash('videosuccess','更新成功');
            $this->redirect('admin/video');
        }
    }
}