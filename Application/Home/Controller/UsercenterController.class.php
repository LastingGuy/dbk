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

    //用户中心主页
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

    //所有订单
    public function allorder()
    {
        if(session('?weixin_user'))
        {
            // test
            // $openid = 'oF6atwMLdDGJg_5NHyy0PBfeg0RU';
            
            $openid = session('weixin_user');

            $orders = $this->getOrders($openid,0,0);
            $this->assign('count',count($orders));
            $this->assign('recoder',$orders);
            $this->display();
        }
        else
        {
            $this->error('请登录！');
        }
    }

    //未完成订单
    public function unfinishedorder()
    {
        if(session('?weixin_user'))
        {
            // test
            // $openid = 'oF6atwMLdDGJg_5NHyy0PBfeg0RU';
            
            $openid = session('weixin_user');

            $orders = $this->getOrders($openid,0,2);
            $this->assign('count',count($orders));
            $this->assign('recoder',$orders);
            $this->display();
        }
        else
        {
            $this->error('请登录！');
        }
    }


    //已完成订单
    public function finishedorder()
    {
        if(session('?weixin_user'))
        {
            // test
            // $openid = 'oF6atwMLdDGJg_5NHyy0PBfeg0RU';
            
            $openid = session('weixin_user');

            $orders = $this->getOrders($openid,0,3);
            $this->assign('count',count($orders));
            $this->assign('recoder',$orders);
            $this->display();
        }
        else
        {
            $this->error('请登录！');
        }
    }

    
    ///获得订单
    private function getOrders($openid,$type,$status)
    {
        //type: 0:代取快递订单 1：代寄快递
        //status：0：all， 2：待收获 ，3：完成
        if($type!=0 && $type!=1)
        {
             return false;
        }
        if($status!=0 && $status!=2 && $status!=3)
        {
            return false;
        }

        if($type==0)
        {
            $pickupModel = M('pickup');
            if($status==0)
            {
                $datas = $pickupModel->where("openid='$openid'")->order('time desc')->select();
                return $datas;
            }
            else
            {
                $datas = $pickupModel->where("openid='$openid' and express_status='$status'")->order('pickup_id desc')->select();
                return $datas;
            }
        }


    }
}
?>