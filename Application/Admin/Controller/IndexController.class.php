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
}