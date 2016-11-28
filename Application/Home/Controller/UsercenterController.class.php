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

    private $orderDAO;
    private $userDAO;
    public function __construct()
    {
        parent::__construct();

        // test
        // $openid = 'oF6atwIKrnG44UaIGPsSGDZUGmmk';
        // session('weixin_user',$openid);


        $this->orderDAO = new Common\OrderDAOlmpl();

    }

    public function index()
    {
        
        //查看用户是否已经写入数据库，没有则写入
        $object = new Common\UserDAOImpl();
        if($object->login())
        {
            $this->redirect('usercenter');
        }
        else
        {
            $this->error('请登录！');
        }
    }

    //用户中心主页
    public function usercenter()
    {

        //test
        // $this->display();
//         $this->redirect('index/order');

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

    //用户中心主页
    public function usercentertest()
    {

        //test
        // $this->display();
//        $this->redirect('index/order');

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
            $this->display('usercenter');
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
            // $openid = 'oF6atwIKrnG44UaIGPsSGDZUGmmk';
            // session('weixin_user',$openid);
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
            // $openid = 'oF6atwIKrnG44UaIGPsSGDZUGmmk';
            
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
            // $openid = 'oF6atwIKrnG44UaIGPsSGDZUGmmk';
            
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

    //获得订单详情
    public function orderdetail()
    {
        if(session('?weixin_user'))
        {
            $type = I('get.type');
            $id = I('get.id');
            $page = I('get.page');
            $openid = session('weixin_user');
            C("READ_DATA_MAP",true); //启用模板映射
            $data = array();
            switch($type)
            {
                ///type 为1查询代取快递订单 2查询待寄快递订单
                case 0:
                    Common\WeixinPayUtil::weixinRefundQuery($id);
                    $model = D('orderdetail');
                    $data = $model->where("pickup_id='$id' and openid='$openid'")->select();   
                    break;
                case 1:
                    $model = D('sendView');
                    $data = $model->where("send_id='$id' and openid='$openid'")->select();
                    break;
                default:
                    $this->error('无效订单');
            }
            if(count($data)==0)
            {
                 $this->error('无效订单');
            }
            else
            {
                // print_r($data);
                $this->assign('page',$page);
                $this->assign('data',$data[0]);
                $this->display();
            }
        }
        else
        {
            $this->error('请登录！');
        }
        
    }

    //删除订单
    //return code:
    //0:没有登陆
    //1：删除成功
    //2：id不正确
    //4:无此订单
    //5:该时段订单无法删除
    //6:删除失败
    public function deleteorder()
    {
        if(session('?weixin_user'))
        {
            $type = I('post.type');
            $id = I('post.id');
            $result = array();
            // $type = 0;
            // $id = "1320";
            // $result['error_code'] = '123';
            if($id=='')
            {
                $response = new Common\ResponseGenerator('deleteOrder',false,2,"ID不正确");
                $this->ajaxReturn($response->generate());
            }


            switch($type)
            {
                case 0:
                    $this->ajaxReturn($this->orderDAO->deletePickupOrder($id)->generate());
                    // $this->ajaxReturn($result);
                case 1:
                    $this->ajaxReturn($this->orderDAO->deleteSendOrder($id)->generate());
                default:
                    $this->ajaxReturn('6');

            }
        }
        else
        {   
            $response = new Common\ResponseGenerator('deleteOrder',false,0,"请登录");
            $this->ajaxReturn($response->generate());
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

        return $this->orderDAO->getOrders($type,$status);
    }
}
?>