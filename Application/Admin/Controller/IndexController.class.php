<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Common;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }

    //登录
    public  function login(){
        $user_id = I("post.userId");
        $user_passwd = I("post.passwd");

        //进行验证
        $object = new Common\AdminDAOImple();
        $return_data =   array();
        $return_data["login"] = $object->login($user_id,$user_passwd);

        return $return_data;
    }
}