<?php
namespace Home\Controller;
use Think\Controller;
use Home\Common;

/*
Author:Wang Jinglu
Date: 2016/10/25
Deccription:用户中心
*/
class UsercenterController extends Controller
{
    public function index()
    {
               //查看用户是否已经写入数据库，没有则写入
        $object = new Common\UserDAOImpl();
        if($object->login())
        {
            $this->redirect('home/usercenter/usercenter');
        }
        else
        {
            $this->error('请登录！');
        }
    }
    public function usercenter()
    {
        $user = new Common\UserDAOImpl();
        if( $user->getUserInfo())
        {
            $nikename = session('user_name');
            $headimgurl = session('headimgurl');
            if($headimgurl=='')
            {
                $headimgurl='__PUBLIC__\img\123.png';
            }
            $this->assign('nikename',$nikename);
            $this->assign('headimgurl',$headimgurl);
            $this->display();
        }
        else
        {
            $this->error('请登录！');
        }

    }
}
?>