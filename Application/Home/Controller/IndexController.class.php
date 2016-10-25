<?php
namespace Home\Controller;
use Think\Controller;
use Home\Common;
class IndexController extends Controller{
    //登陆
    public function index()
    {
        if(IS_GET)
        {
            //查看用户是否已经写入数据库，没有则写入
            $object = new Common\UserDAOImpl();
            if($object->login())
            {
                $this->redirect('home/index/order');
            }
            else
            {
                $this->error('请登录！');
            }
        }
        else
        {
            $this->error('请登录！');
        }
    }

    //订单界面
    public function order()
    {
        if(!session('?weixin_user'))
        {
            $this->error('请登录！');
        }
        else
        {
            $this->display();
        }
    }

    #create the order of receiving mail
    public function newRecvOrder()
	{
        //验证是否登陆
        if(!session('?weixin_user'))
        {
            $this->ajaxReturn('请登录！');
        }

        
        if(IS_POST)
        {
            $data = I('post.');
            $school = $data['school'];
            $city = $data['city'];
            $address = $data['address'];

            ///获得寝室id
            $DOR = D('DormitoryView'); //实例化寝室模型
            $dor = $DOR->field('dormitory_id')->where("school_name='$school' and school_city='$city' and dormitory_address = '$address'")->select();
            if(count($dor)>0)
            {
                $dor = $dor[0]['dormitory_id'];
                $data['dor'] = $dor;
            }
            else
            {
                $this->ajaxReturn('请填写正确的收货人地址');
            }

            //写入数据库
            $data['receiver_name'] = $data['rename'];
            $data['receiver_phone'] = $data['tel'];
            $data['dormitory_id'] = $data['dor'];
            $data['express_company'] = $data['express'];
            $data['express_code'] = $data['fetch_code'];
            $data['time'] = date('Y-m-d H:i:s');
            $data['openid'] = session('weixin_user');
            $data['express_status'] = 2;

            $pickup = D('pickup');
            if($pickup->create($data))
            {
                if($pickup->add($data))
                {
                    $this->ajaxReturn('提交成功'); 
                }
                else
                {
                    $this->ajaxReturn('提交失败');
                }
            
            }
            else
            {
                 $this->ajaxReturn($pickup->getError());
            }

        }
        else
        {
            $this->ajaxReturn('提交失败');
        }
    }

    //创建代取快递订单
    public function newSendOrder()
    {
        //验证是否登陆
        if(!session('?weixin_user'))
        {
            $this->ajaxReturn('请登陆！');
        }


        if(IS_POST)
        {
            $send = D('send');
            $data = I('post.');

            $school = $data['school'];
            $city = $data['city'];
            $address = $data['address'];

            ///获得寝室id
            $DOR = D('DormitoryView'); //实例化寝室模型
            $dor = $DOR->field('dormitory_id')->where("school_name='$school' and school_city='$city' and dormitory_address = '$address'")->select();
            if(count($dor)>0)
            {
                $dor = $dor[0]['dormitory_id'];
                $data['dor'] = $dor;
            }
            else
            {
                $this->ajaxReturn('请填写正确的收货人地址');
            }

            ///写入数据库
            $data['sender_name'] = $data['rename'];
            $data['sender_phone'] = $data['tel'];
            $data['dormitory_id'] = $data['dor'];
            $data['sender_goods'] = $data['delivery'];
            $data['time'] = date('Y-m-d H:i:s');
            $data['openid'] = session('weixin_user');
            $data['sender_status'] = 2;

            // var_dump($data);
            if($send->create($data))
            {
                if($send->add($data))
                {
                    print('提交成功');
                }
                else
                {
                    print('提交失败');
                }
            }
            else
            {
                print($send->getError());
            }
        }
        else
        {
           print('提交失败！');
        }
    }

}
?>