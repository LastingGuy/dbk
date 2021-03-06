<?php

/*
*   Author:Wang Jinglu
*   Date:2016/10/31
*/
namespace Home\Common;
import("Org.WeixinPay.WxPay#Api",null,".php");

class OrderDAOlmpl implements IOrderDAO
{
    //Types of Order
    const ALLKindsOFOrder = 0;
    const PICKUPORDER = 1;
    const SENDORDER = 2;

    //status
    const ALL=0;
    const UnPaid = 1;
    const UnFinished = 2;
    const Finished = 3;
    const Refunding = 4;
    const Cancelled = 5;
    const Deleted = 100;


    private $openid;    //当前用户openid
    private $sendModel; //M('send')
    private $pickupModel;   //M('pickup')
    private $orderDetail;   //D('orderdetail')
    private $sendOrder; //D('sendView')
    private $M_weixinPay; //M('weixinPay')
    private $M_sendView;    //M('sendView')
    private $D_orderdetail; //D('orderdetail')

    const judgeTime = 16;

    public function __construct()
    {
        $this->openid = session('weixin_user');
        $this->sendModel = M('send');
        $this->pickupModel = M('pickup');
        $this->orderDetail = D('orderdetail');
        $this->sendOrder = D('sendView');
        $this->M_weixinPay = M('weixinPay');
        $this->M_sendView = M('sendView');
        $this->D_orderdetail = D('orderdetail');

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
            //当前时间点在16：00之前，将时间戳减去一天，如果当天是星期天，则将时间戳减去两天
            //如果当天为星期六，时间戳为星期五的16：00
            $stamp -= 24 * 60 * 60;
            if($today['wday']==0)
            {
                $stamp -= 24 * 60 * 60;
            }
        }
       
        $order = $model->where("openid='%s' and pickup_id='%s'",$this->openid,$id)->find();

        $ordertime = strtotime($order['time']);

        if($order['express_status']!=2 && $order['express_status']!=4 && $order['express_status']<100)
        {
            //未支付、已完成可以删除
            if($model->where("pickup_id='$id'")->setInc('express_status',100)==true)
            {
                return $response->setSuccess(true)->setCode(1)->setMsg("删除成功");
            }     
            else
            {
                return $response->setCode(6)->setMsg("删除失败");
            }

            
        }
        else if($order['express_status']==2)
        {   //未完成订单
            
            if($ordertime>$stamp)
            {
                //在可删除时间之后的订单，可以删除

                $result = new ResponseGenerator("deletePickUpOrder",true);
                if($order['price']!=0)
                {
                    //退款操作
                    $result = WeixinPayUtil::refundRecvOrder($id);
                    //修改状态
                    if($result->getSuccess())
                    {
                        $model->where("pickup_id='$id'")->setField('express_status',4);
                        $result->setSuccess(true)->setCode(1)->setMsg("申请退款成功")->setAction('deletePickUpOrder');
                    }

                    return $result;
                }
                else
                {
                    $model->where("pickup_id='$id'")->setField('express_status',5);
                    return $result->setSuccess(true)->setCode(1)->setMsg("删除成功");
                }        
                
            }
            else
            {
                //无法删除
                return $response->setSuccess(false)->setCode(5)->setMsg('该时段无法删除');
            }
        }
        else
        {
            return $response->setSuccess(false)->setCode(6)->setMsg('无法删除订单');
        }

       
    }

    //删除待寄订单
    public function deleteSendOrder($id)
    {
        $response = new ResponseGenerator('deleteSendOrder');

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


        $order = $model->where("openid='%s' and send_id='%s'",$this->openid,$id)->find();

        $ordertime = strtotime($order['time']);

        if($order['sender_status']!=2 && $order['sender_status']!=4 && $order['sender_status']<100)
        {
            //未支付、已完成可以删除
            if($model->where("send_id='$id'")->setInc('sender_status',100)==true)
            {
                return $response->setSuccess(true)->setCode(1)->setMsg("删除成功");
            }
            else
            {
                return $response->setCode(6)->setMsg("删除失败");
            }


        }
        else if($order['sender_status']==2)
        {   //未完成订单

            if($ordertime>$stamp)
            {
                //在可删除时间之后的订单，可以删除

                $result = new ResponseGenerator("deletePickUpOrder",true);

                //if($order['price']!=0) //代寄订单没有价格信息，不进行退款操作
                if(false)
                {
                    //退款操作
                    $result = WeixinPayUtil::refundRecvOrder($id);
                    //修改状态
                    if($result->getSuccess())
                    {
                        $model->where("send_id='$id'")->setField('sender_status',4);
                        $result->setSuccess(true)->setCode(1)->setMsg("申请退款成功")->setAction('deletePickUpOrder');
                    }

                    return $result;
                }
                else
                {
                    $model->where("send_id='$id'")->setField('sender_status',5);
                    return $result->setSuccess(true)->setCode(1)->setMsg("取消成功");
                }

            }
            else
            {
                //无法删除
                return $response->setSuccess(false)->setCode(5)->setMsg('该时段无法删除');
            }
        }
        else
        {
            return $response->setSuccess(false)->setCode(6)->setMsg('无法删除订单');
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


    /**根据状态码获得代取订单
     *
     *暂时只支持 ALL/UnFinished/Finished状态的订单查询
     * @param $status 订单状态（ALL/UnFinished/Finished）
     * @return array 订单详情
     */
    public function getPickupOrdersByStatus($status)
    {
        switch($status)
        {
            case self::ALL:     //获得所有状态的订单
                return $this->getAllPickupOrders();
                break;
            case self::UnFinished:  //获得未完成订单
                return $this->getUnfinishedPickupOrders();
                break;
            case self::Finished:    //获得已完成订单
                return $this->getFinishedPickupOrders();
                break;
            default:
                return array();
        }
    }


    //获得所有代寄订单
    public function getAllSendOrders()
    {
        $datas = $this->sendOrder->where("openid='$this->openid' and sender_status<100")->order('time desc')->select();
        return $datas;
    }

    //获得未完成代寄订单
    public function getUnFinishedSendOrders()
    {
        $datas = $this->sendOrder->where("openid='$this->openid' and express_status=2")->order('time desc')->select();
        return $datas;
    }

    //获得已完成代寄订单
    public function getFinishedSendOrders()
    {
        $datas = $this->sendOrder->where("openid='$this->openid' and express_status=3")->order('time desc')->select();
        return $datas;
    }

    /**
     * 根据状态码获得代寄订单
     *
     * 暂时只支持 ALL/UnFinished/Finished状态的订单查询
     * @param $status 订单状态（ALL/UnFinished/Finished）
     * @return array 订单详情
     */
    public function getSendOrdersByStatus($status)
    {
        switch ($status)
        {
            case self::ALL: //获得所有代寄订单
                return $this->getAllSendOrders();
            case self::UnFinished:  //获得所有未完成订单
                return $this->getUnFinishedSendOrders();
            case self::Finished:
                return $this->getFinishedSendOrders();
            default:
                return array();
        }

    }


    /**
     * 获得订单详情
     * @param $type(PICKUPORDER/SENDORDER) 订单类型
     * @param $orderid 订单号
     * @return 订单详情
     */
    public function getOrderDetail($type,$orderid)
    {
        switch ($type)
        {
            case self::PICKUPORDER: //代取订单
                $orderModel = $this->D_orderdetail;
                $data = $orderModel->where("pickup_id='%s' and openid='%s' and express_status<100",$orderid,$this->openid)->select();
                return $data;
            case self::SENDORDER:   //代寄订单
                $orderModel = $this->M_sendView;
                $data = $orderModel->where("send_id='%s' and openid='%s' and sender_status<100",$orderid,$this->openid)->select();
                return $data;
            default:
                return array();
        }
    }



//////////////////////////////未完成/////////////////
    public function getAllOrders()
    {
        return;
    }
    public function getDeleteOrders()
    {
        return;
    }
///////////////////////////////////////////////////////


    /**
     * 根据类型、状态获得订单详情
     * @type 订单类型（ALLKindsOFOrder/PICKUPORDER/SENDORDER）
     * @status 订单状态 暂时只支持ALL、UnFinished、Finished状态的查询
     * @return 订单详情
    */
    public function getOrders($type,$status)
    {
        switch($type)
        {
            case self::ALLKindsOFOrder:     //获取所有订单
                    //暂只获得全部代取订单
            case self::PICKUPORDER:     //获取代取订单
                return $this->getPickupOrdersByStatus($status);
            case self::SENDORDER:     //获得代寄订单
                return $this->getSendOrdersByStatus($status);
            default:
                return array();
        }
    }

    //新建待取订单
    //return ResponseGenerator
    //code :
    //  0: 失败
    //  1：成功需要支付
    //  2：成功，价格为0不需要进行支付
    public function newRecvOrder()
    {
        $data = I('post.');
        $school = $data['school'];
        $city = $data['city'];
        $address = $data['address'];

        
        $response = new ResponseGenerator("NewRecvOrder");

        //获得寝室id
        $DOR = D('DormitoryView'); //实例化寝室模型
        $dor = $DOR->field('dormitory_id')->where("school_name='$school' and school_city='$city' and dormitory_address = '$address'")->select();
        if(count($dor)>0)   //找到符合的寝室
        {
            $dor = $dor[0]['dormitory_id'];
            $data['dor'] = $dor;
        }
        else    //未找到符合寝室
        {   
            return $response->setCode(0)->setMsg('请填写正确的收货人地址');
        }

        //计算价格
        $price = $this->charge($school,$data['express_type']);
        if($price==-100)
        {
            return $response->setCode(0)->setMsg('价格错误,请尝试重新下单');
        }
        else
        {
            $data['price'] = $price;;
        }

        
        //构建数据库信息
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
        if($price==0)   //价格为0。不需要支付
        {
            $data['express_status'] = 2;
            $data['pay_time']=$data['time'];
            $response->setCode(2)->setMsg('价格为0，不需要进行支付');
        }
        else    //需要支付
        {
            $data['express_status'] = 1;
            $response->setCode(1)->setMsg('下单成功，进行支付');
        }



        $pickup = D('pickup');
        if($pickup->create($data))
        {
            if($id = $pickup->add($data))
            {
                \Think\Log::write($pickup->getLastSql(),'INFO');
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
                $info = $pickup->where('pickup_id=%s',$id)->find();
                if($info==null)
                {
                    return $response->setSuccess(false)->setMsg('系统错误,请在订单详情中支付');
                }
                else
                {
                    return $response->setSuccess(true)->setBody($info);
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

    //新建代寄订单
    public function newSendOrder()
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
            $this->ajaxReturn($send->getError());
        }
    }

    /**
     * 获得未支付订单信息
     * @param $orderid 订单号
     * @param $orderType 订单类型 1 为代取订单 2 为代寄订单
     * @param $userid 用户 openID
     * @return ResponseGenerator
     * */
    public function getUnPaidOrderInfo($orderid,$orderType,$userid)
    {
        $weixinPayModel = $this->M_weixinPay;
        $response = new ResponseGenerator("getUnPaidOrderInfo");
        switch ($orderType)
        {
            case 1:
            case 2:
                $order = $weixinPayModel->where("order_id=%s and openid='%s' and pay_type=%s",$orderid,$userid,$orderType)->find();
                if($order)
                {
                    if($order['pay_status']!=0)
                    {
                        return $response->setCode(0)->setMsg("订单以支付");
                    }
                    else if(time()>strtotime($order['time_expire']))
                    {
                        return $response->setCode(0)->setMsg("订单过期");
                    }

                    $jsData = new \WxPayJsApiPay();
                    $jsData->SetAppid(\WxPayConfig::APPID);
                    $timestamp = time();
                    $jsData->SetTimeStamp("$timestamp");
                    $jsData->SetNonceStr(\WxPayApi::getNonceStr());
                    $jsData->SetPackage("prepay_id=" . $order['prepay_id']);
                    $jsData->SetSignType("MD5");
                    $jsData->SetPaySign($jsData->MakeSign());
                    $parameters = json_encode($jsData->GetValues());
                    return $response->setSuccess(true)->setCode(1)->setMsg("ok")->setBody($parameters);

                }
                break;
            default:
                return $response->setCode(0)->setMsg("参数错误");
        }

    }


    //判断是否为手机号码
    private function isMobile($mobile) 
    {
        $is_tel = preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$mobile)?true:false;
        if($is_tel)
        {
            return true;
        }

        if (!is_numeric($mobile)) 
        {
            return false;
        }     
        $is_mobile =preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
        return $is_mobile;
    }
    /**
     *验证是否存在相同订单
     *$data is an array of the order data, it contains
     *every feild except time and express_status in table pickup or send;
     *$type is defined to check which kind of order(0 for pickup order, 1 for send order)
     *it retruns whether there is a same order in db
     */
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
        $charge = getPrice($school,$type,true);

        if($charge==-1)
        {
            return false;
        }
        else
        {
            $type = $charge['description'];
            return $charge['price'];
        }
    }
}