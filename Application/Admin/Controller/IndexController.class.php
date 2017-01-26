<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Common;

class IndexController extends Controller {
    public function index(){
        $this->display();
    }

    //登录
    public function login(){
        $user_id = I("post.uid");
        $user_passwd = I('post.password');

        $object = new Common\AdminDAOImpl();
        $return_data['login'] = $object->login($user_id,$user_passwd);
        $this->ajaxReturn($return_data);
    }

    public function test()
    {
//        $schoolDao = new Common\DAO\DormitoryDAO();
        //$s = $schoolDao->findByID(1);
//        $this->ajaxReturn($s);


//        $s =$schoolDao->clear()->findByID(1);
        $s = new Common\Management\SchoolManagement();
//        $this->ajaxReturn($s);

        $dor = new Common\DAO\DormitoryDAO(1);
        echo $dor->update();
    }
}