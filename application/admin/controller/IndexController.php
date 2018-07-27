<?php
namespace app\admin\controller;

use think\Controller;

class IndexController extends Controller
{
    // 二维码
    public function qrcode()
    {
        $savePath = APP_PATH . '/../Public/qrcode/';
        $webPath = '/Weblearning/public/qrcode/';
        $qrData = 'https://blog.csdn.net/blank__box';
        $qrLevel = 'H';
        $qrSize = '8';
        $savePrefix = 'NickBai';

        if($filename = createQRcode($savePath, $qrData, $qrLevel, $qrSize, $savePrefix)){
            $pic = $webPath . $filename;
        }
        echo "<img src='".$pic."'>";
    }
    public function index(){
        $myfile = fopen("testfile.txt", "w");
        fwrite($myfile,             "C:\website\WWW"."\\".$_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], "D:\website\WWW"."\\".$_FILES["file"]["name"]);
//        $file_path = "D:\website\WWW"."\\";
//        $var = $_POST['result'];
//        $file_path = $file_path . basename( $_FILES['uploaded_file']['name']);
//        if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path.$_FILES['uploaded_file']['tmp_name'])) {
//            $result = array("result" => "success", "value" => $var);
//        } else{
//            $result = array("result" => "error");
//        }
//        echo json_encode($result);
    }
    public function findpwd(){
        if(request()->isPost()){
            $email=input('email');
            //该邮箱是否存在
            $emailinfo=Db::name("users")->where("email",$email)->count();
            if($emailinfo){
                $title = "找回密码通知！";
                $srand = rand ( 11111, 99999 );
                $data ['rand'] = $srand;
                Db::name("users")->where ( "email = '$email'" )->update ( $data ); // 更新数据库
                // echo Db::name("users")->getLastSQL();die();
                $content = "校验码:$srand";

                if (SendMail ( $email, $title, $content )) {
                    $this->success( "发送成功,请到邮箱查看校验码!", url('setpwd',['email'=>$email]) );
                    die ();
                } else {
                    $this->error( $email->ErrorInfo );
                    die ();
                }
            }else{
                $this->error("邮箱不存在");
            }
            //如果存在，则生成四位随机数，发到该邮箱
        }
        return $this->fetch();
    }
    public function setpwd(){
        if(request()->isPost()){
            $rand=input('rand');
            $newpwd=input('newpwd');
            $dbrand=Db::name("users")->where("email",session("email"))->find();
            // var_dump($dbrand);
            if($rand!=$dbrand["rand"]){
                $this->success("验证码错误");
            }else{
                Db::name("users")->where("email",session("email"))->update(["password"=>md5($newpwd)]);
                $this->success('重设密码成功',"index");
            }
        }else{
            $email=input("email");
            session("email",$email);
        }
        return $this->fetch();
    }
}