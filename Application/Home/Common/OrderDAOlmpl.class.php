<?php

/*
*   Author:Wang Jinglu
*   Date:2016/10/31
*/
namespace Home\Common;
import("Org.WeixinPay.WxPay#Api",null,".php");

class OrderDAOlmpl implements IOrderDAO
{
    private $openid;
    private $sendModel;
    private $pickupModel;
    private $orderDetail;
    private $sendOrder;
    const judgeTime = 16;

    public function __construct()
    {
        $this->openid = session('weixin_user');
        $this->sendModel = M('send');
        $this->pickupModel = M('pickup');
        $this->orderDetail = D('orderdetail');
        $this->sendOrder = D('sendView');

    }
    
    public function setOpenID($id='')
    {
        if($id!='')
        {
            $this->openid = $id;
        }
        else
        {
            $this->openid = session('weixin_user');
        }
    }

    //删除代取订单
    public function deletePickupOrder($id)
    {
       $response = new ResponseGenerator("deletePickUpOrder");


        $model = $this->pickupModel;
        $today = getdate();
        $stamp = mktime(self::judgeTime,0,0,$today['mon'],$today['mday'],$today['year']);

        if($today['hours']<self::judgeTime || $today['wday']==6)
        {
            $stamp -= 24 * 60 * 60;
            if($today['wday']==0)
            {
                $stamp -= 24 * 60 * 60;
            }
        }
        $deadLine = date('Y-m-d H:i:s',$stamp);

        $unfinished = $model->where("openid='$this->openid' and pickup_id='$id' and express_status=2  and time>'$deadLine'")->find();
        $finishedAndunpaid = $model->where("openid='$this->openid' and pickup_id='$id' and (express_status=3 or express_status=1 or express_status=5)")->find();
        $str = $model->getLastSQL();
        if($finishedAndunpaid == null && $unfinished==null)
        {
            return $response->setCode(5)->setMsg("该时段订单无法删除");
        }

        if($unfinished!=null)
        {
            
            //微信支付申请退款
            //查看是否有该订单的微信支付
            $mod = M("weixin_pay");
            if($pay = $mod->where("order_id=$id and pay_type=1")->find()) 
            {
                $sql1 = $mod->getLastSQL();
                //商户退款单号 日期+类型（10代表代取订单、退款）+订单号
                $out_refund_no = \WxPayConfig::MCHID.date("Ymd").'10'.$id;
                //向微信申请退款
                $input = new \WxPayRefund();
                $input->SetOut_trade_no($out_refund_no);
                $input->SetTotal_fee($pay['total_fee']);
                $input->SetRefund_fee($pay['total_fee']);
                $input->SetOut_refund_no(\WxPayConfig::MCHID . date("YmdHis"));
                $input->SetOp_user_id(\WxPayConfig::MCHID);
                $result = \WxPayApi::refund($input);

                //退款成功
                if($result['return_code']=="SUCCESS")
                {
                    //查找是否有退款记录，如果没有退款记录则插入
                    $mod = M("weixin_refund");
                    if (!($refund = $mod->where("trade_no=" + $pay['trade_no'])->find())) 
                    {
                        $refund['refund_no'] = $out_refund_no;
                        $refund['openid'] = $pay['openid'];
                        $refund['trade_no'] = $pay['trade_no'];
                        $refund['total_fee'] = $pay['total_fee'];
                        $refund['refund_time'] = $result['refund_time'];
                        $refund['refund_id'] = $result['refund_id'];
                        $mod->add($refund);                  
                    }
                    $sql2 = $mod->getLastSQL();
                    $model->where("pickup_id='$id'")->setField('express_status',4);
                    return $response->setSuccess(true)->setCode(1)->setMsg("申请退款成功")->setBody(
                        array(
                            'sql1'=>$sql1,
                            'sql2'=>$sql2,
                            'pay'=>$pay,
                            'result'=>$result,
                            'refund'=>$refund
                        )
                        );
                }
                else
                {
                    return $response->setCode(6)->setMsg("申请退款失败");
                }

            }
            else
            {
                return $response->setCode(6)->setMsg("申请退款失败");
            }

        }

        if($finishedAndunpaid!=null)
        {
            if($model->where("pickup_id='$id'")->setInc('express_status',100)==true)
            {
                return $response->setSuccess(true)->setCode(1)->setMsg("删除成功");
            }     
            else
            {
            return $response->setCode(6)->setMsg("删除失败");
            }
        }

        return $response->setCode(6)->setMsg("删除失败");
       
    }

    //删除待寄订单
    public function deleteSendOrder($id)
    {
        $model = $this->sendModel;
        $today = getdate();
        $stamp = mktime(self::judgeTime,0,0,$today['mon'],$today['mday'],$today['year']);

        if($today['hours']<self::judgeTime || $today['wday']==6)
        {
            $stamp -= 24 * 60 * 60;
            if($today['wday']==0)
            {
                $stamp -= 24 * 60 * 60;
            }
        }
        $deadLine = date('Y-m-d H:i:s',$stamp);

        $unfinished = $model->where("send_id='$this->openid' and pickup_id='$id' and sender_status=2  and time>'$deadLine'")->find();
        $finished = $model->where("send_id='$this->openid' and pickup_id='$id' and sender_status=3")->find();
        if($finished == false && $unfinished==false)
        {
            return 5;
        }

        if($model->where("pickup_id='$id'")->setInc('sender_status',100)==true)
        {
            return 1;
        }     
        else
        {
           return 6;
        }
    }

    //获得所有未删除代取订单
    public function getAllPickupOrders()
    {
        $datas = $this->pickupModel->where("openid='$this->openid' and express_status<100")->order('time desc')->select();
        return $datas;
    }

    //获得未完成订单
    public function getUnfinishedPickupOrders()
    {
        $datas = $this->pickupModel->where("openid='$this->openid' and express_status=2")->order('pickup_id desc')->select();
        return $datas;
    }

    //获得已完成订单
    public function getFinishedPickupOrders()
    {
        $datas = $this->pickupModel->where("openid='$this->openid' and express_status=3")->order('pickup_id desc')->select();
        return $datas;
    }
    public function getPickupOrdersByStatus($status)
    {
        switch($status)
        {
            case 0:
                return $this->getAllPickupOrders();
                break;
            case 2:
                return $this->getUnfinishedPickupOrders();
                break;
            case 3:
                return $this->getFinishedPickupOrders();
                break;
            default:
                return array();
        }
    }

    public function getAllSendOrders()
    {
        $datas = $this->sendOrder->where("openid='$this->openid' and express_status<100")->order('time desc')->select();
        return $datas;
    }
    public function getAllOrders()
    {
        return;
    }
    public function getDeleteOrders()
    {
        return;
    }

    public function getOrders($type,$status)
    {
        switch($type)
        {
            case 0: 
                return $this->getPickupOrdersByStatus($status);
                break;
            case 1:
                return $this->getPickupOrdersByStatus($status);
                break;
            default:
                return array();
        }
    }

    public function newRecvOrder()
    {
        $data = I('post.');
        $school = $data['school'];
        $city = $data['city'];
        $address = $data['address'];

        $response = new ResponseGenerator("NewRecvOrder");
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
            return $response->setCode(0)->setMsg('请填写正确的收货人地址');
        }

        //计算价格
        $price = $this->charge($school,$data['express_type']);
        if($price==-100)
        {
            return $response->setCode(0)->setMsg('订单错误');
        }
        else
        {
            $data['price'] = $price;;
        }

        
        //写入数据库
        $data['receiver_name'] = $data['rename'];
        if(!$this->isMobile($data['tel']))
        {
            return $response->setCode(0)->setMsg('请正确填写手机号!');
        }
        $data['receiver_phone'] = $data['tel'];
        $data['dormitory_id'] = $data['dor'];
        $data['express_company'] = $data['express'];
        $data['express_code'] = $data['fetch_code']; 
        $data['openid'] = session('weixin_user');
        

        $data['time'] = date('Y-m-d H:i:s');
        $data['express_status'] = 1;



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
                $info = $pickup->where($data)->find();
                if($info==null)
                {
                    return $response->setCode(0)->setSuccess(false)->setMsg('订单错误');
                }
                else
                {
                    return $response->setCode(1)->setSuccess(true)->setMsg('下单成功')->setBody($info);
                }
            }
            else
            {
                return $response->setCode(0)->setMsg('提交失败');
            }
        
        }
        else
        {
            return $response->setCode(0)->setMsg($pickup->getError());
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