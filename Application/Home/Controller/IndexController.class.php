<?php
namespace Home\Controller;
use Think\Controller;
use Home\Common;
class IndexController extends Controller{
    //登陆
    public function index()
    {
        // test
        // $openid = 'oF6atwNyAc4wlpgNVWTdQi4kj7Po';
        // session('weixin_user',$openid);
        // $this->redirect('home/index/order');

        if(I("get.code")!='')
        {
            //查看用户是否已经写入数据库，没有则写入
            $object = new Common\UserDAOImpl();
            if($object->login())
            {
                $this->redirect('order');
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
            $this->getDefaultInfo();
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

            //计算价格
            $price = $this->charge($school,$data['express_type']);
            if($price==-100)
            {
                $this->ajaxReturn('订单错误!');
            }
            else
            {
                $data['price'] = $price;;
            }

            
            //写入数据库
            $data['receiver_name'] = $data['rename'];
            if(!$this->isMobile($data['tel']))
            {
                $this->ajaxReturn('请填写正确的手机号！');
            }
            $data['receiver_phone'] = $data['tel'];
            $data['dormitory_id'] = $data['dor'];
            $data['express_company'] = $data['express'];
            $data['express_code'] = $data['fetch_code']; 
            $data['openid'] = session('weixin_user');
            

            $data['time'] = date('Y-m-d H:i:s');
            $data['express_status'] = 2;



            $pickup = D('pickup');
            if($pickup->create($data))
            {
                if($pickup->add($data))
                {
                    if($data['default']=='true')
                    {
                        $info = array(
                            'default_name'=>$data['receiver_name'],
                            'default_phone'=>$data['receiver_phone'],
                            'default_city'=>$city,
                            'default_school'=>$school,
                            'default_dormitory'=>$address
                        );
                        $this->saveDefaultInfo( $data['openid'],$info);
                    }
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

    //创建代寄快递订单
    public function newSendOrder()
    {
        //验证是否登陆
        if (!session('?weixin_user')) {
            $this->ajaxReturn('请登陆！');
        }


        if (IS_POST) 
        {
            $send = D('send');
            $data = I('post.');

            $school = $data['school'];
            $city = $data['city'];
            $address = $data['address'];

            ///获得寝室id
            $DOR = D('DormitoryView'); //实例化寝室模型
            $dor = $DOR->field('dormitory_id')->where("school_name='$school' and school_city='$city' and dormitory_address = '$address'")->select();
            if (count($dor) > 0) {
                $dor = $dor[0]['dormitory_id'];
                $data['dor'] = $dor;
            } else {
                $this->ajaxReturn('请填写正确的寄件人地址');
            }

            ///写入数据库
            $data['sender_name'] = $data['rename'];
            $data['recv_name'] = $data['recvname'];
            if (!$this->isMobile($data['tel'])) {
                $this->ajaxReturn('请填写正确的寄件人手机号！');
            }
            if (!$this->isMobile($data['recvtelephone'])) {
                $this->ajaxReturn('请填写正确的收件人手机号！');
            }
            $data['sender_phone'] = $data['tel'];
            $data['recv_phone'] = $data['recvtelephone'];
            $data['dormitory_id'] = $data['dor'];
            $data['sender_goods'] = $data['delivery'];
            $data['openid'] = session('weixin_user');

            //验证订单是否重复
            // if(!$this->isUniqueOrder($data,1))
            // {
            //     $this->ajaxReturn('已提交相同订单');
            // }

            $data['sender_status'] = 2;
            $data['time'] = date('Y-m-d H:i:s');

            // var_dump($data);
            if ($send->create($data)) 
            {
                if ($send->add($data)) 
                {
                    if ($data['default'] == 'true') 
                    {
                        $info = array(
                            'default_name' => $data['sender_name'],
                            'default_phone' => $data['sender_phone'],
                            'default_city' => $city,
                            'default_school' => $school,
                            'default_dormitory' => $address
                        );

                        $this->saveDefaultInfo( $data['openid'],$info);
                        
                    } 
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


    //判断是否为手机号码
    private function isMobile($mobile) 
    {
        if (!is_numeric($mobile)) 
        {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }

    //验证是否存在相同订单
    //$data is an array of the order data, it contains
    //every feild except time and express_status in table pickup or send;
    //$type is defined to check which kind of order(0 for pickup order, 1 for send order)
    //it retruns whether there is a same order in db
    private function isUniqueOrder($data,$type)
    {
        $r = array();
        switch($type)
        {
            case 0:
                $orderModel = D('pickup');
                $r = $orderModel->where($data)->select();
                break;
            case 1:
                $orderModel = D('send');
                $r = $orderModel->where($data)->select();
                break;
            default:
                return false;
        }
        if(count($r)>0)
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    //设置默认地址
    //$data is an array of the default address info of user,
    //it contains:
    //  $data['openid'],
    //  $data['default_name'],
    //  $data['default_phone'],
    //  $data['default_city'],
    //  $data['default_school'],
    //  $data['default_dormitory']
    //and every element is required
    private function saveDefaultInfo($openid,$data)
    {
        $defaultInfoModel = M('defaultinfo');
        $count = $defaultInfoModel->where("openid='$openid'")->count();
        
        if($count==0)
        {
            ///there is not the default info of this user, add it to db.
            $data['openid'] = $openid;
            $defaultInfoModel->data($data)->add();
        }
        else
        {
            ///there has already the default info of this user ,so replace it by the new info
            $defaultInfoModel->where("openid='$openid'")->save($data);
        }
    }

    //获得默认地址
    private function getDefaultInfo()
    {
        $openid = session('weixin_user');
        $model = M('defaultinfo');
        $data = $model->where("openid='$openid'")->select();
        if(count($data)>0)
        {
            $this->assign('isSetDefault','true');
            $this->assign('city',$data[0]['default_city']);
            $this->assign('school',$data[0]['default_school']);
            $this->assign('dor',$data[0]['default_dormitory']);
            $this->assign('phone',$data[0]['default_phone']);
            $this->assign('name',$data[0]['default_name']);
        }
        else
        {
            $this->assign('isSetDefault','false');
        }
    }


    //计算价格
    private function charge($school,&$type)
    {
        $charge = getPrice($school,$type);
        if($charge==-1)
        {
            $charge=5;
        }

        switch($type)
        {
            case 'size1':
                $type='中小件(<2kg)';
                return $charge;
            case 'size2':
                $type='大件(>2kg)';
                return $charge;
            case 'size3':
                $type='超大件(>3kg)';
                return $charge;
            default:
                return false;
        }
    }
}
?>