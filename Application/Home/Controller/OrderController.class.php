<?php
/**
 * Created by PhpStorm.
 * Author: Ben
 * Date: 2017/1/22
 * Time: 18:05
 * Description:
 *  订单接口
 */
namespace  Home\Controller;
use Home\Common\ResponseGenerator;
use Home\Common\DAO;
use Home\Common\Objects;
use Home\Common\Service\AddressService;
use Home\Common\Service\OrderService;
use Home\Common\Util\PickupPayNotify;
use Think\Controller;
use Think\Crypt\Driver\Think;

class OrderController extends Controller
{
    public  function __construct()
    {
//        测试时使用
//        setUserID('d7eac19a-de42-11e6-93f6-00163e12bad6');
//        setOpenID('oF6atwIKrnG44UaIGPsSGDZUGmmk');
    }

    public function index()
    {
        echo '#^_^#';
    }


    /**
     * 新建代取订单
     */
    public function newPickupOrder()
    {
        //验证是否登录
        if(notSign())
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('newPickupOrder')->generate());

        if(!IS_POST)
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS("newPickupOrder")->setBody(array('description'=>'无提交数据'))->generate());

        $userid = getUserID();
        $data = I('post.');
        if(!$data)
        {
            $r = ResponseGenerator::WRONGPARAMS("newPickupOrder")->setBody(array('description'=>'无提交数据'));
            $this->ajaxReturn($r->generate());
        }
        $order = new Objects\PickupOrder();
        $order->setUserId($userid);
        $order->setSchoolName($data['school']);
        $order->setDormitoryAddress($data['dormitory']);
        $order->setReceiverName($data['receiver']);
        $order->setReceiverPhone($data['phone']);
        $order->setExpressTypeSize($data['size']);
        $order->setExpressCompany($data['company']);
        $order->setExpressSms($data['sms']);
        $order->setExpressCode($data['code']);
        $order->setRemarks($data['remarks']);

        $service = new OrderService();
        $result = $service->newPickupOrder($order);
        if($result===true)
        {
            $body = array(
                'orderNo'=>$order->getPickupNo()
            );
            $r = ResponseGenerator::OK('newPickupOrder');
            $r->setBody($body);
            $this->ajaxReturn($r->generate());
        }
        else
        {
            $body = array(
                'description'=>$result
            );
            $r = ResponseGenerator::WRONGPARAMS('newPickupOrder');
            $r->setBody($body);
            $this->ajaxReturn($r->generate());
        }
    }

    /**微信支付代取订单
     *
     */
    public function weixinPayPickupOrder()
    {
        //验证登录
        if(notSign())
        {
            $this->ajaxReturn(ResponseGenerator::NOTSIGN("weixinPayPickupOrder")->generate());
        }

        //验证是否为post提交
        if(!IS_POST)
        {
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS("weixinPayPickupOrder")->generate());
        }

        //验证参数
        $orderNo = I('post.orderNo');
        if(!$orderNo)
        {
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS("weixinPayPickupOrder")->generate());
        }

        $orderService = new OrderService();
        $pickupPay = $orderService->getPickupPay($orderNo);
        if($pickupPay===false)
        {
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS("weixinPayPickupOrder")->generate());
        }

        $fee = $pickupPay->getPayFee();
        if($fee!==null)
        {
            $r = new ResponseGenerator("weixinPayPickupOrder");
            $r->setCode(40)->setSuccess(false)->setMsg("Fail");
            $this->ajaxReturn($r->generate());
        }

        if($orderService->pickupOrder_freeOrder($pickupPay)!==false)
        {
            $r = new ResponseGenerator("weixinPayPickupOrder");
            $r->setCode(41)->setSuccess(true)->setMsg("FreeOrder");
            $this->ajaxReturn($r->generate());
        }

        if($orderService->pickupOrder_withCoupon($pickupPay)!==false)
        {
            $r = new ResponseGenerator("weixinPayPickupOrder");
            $r->setCode(42)->setSuccess(true)->setMsg("UseCoupon");
            $this->ajaxReturn($r->generate());
        }

        $body = $orderService->pickupOrder_WexinPay($orderNo,$pickupPay);
        if($body!==false)
        {
            $r = new ResponseGenerator("weixinPayPickupOrder");
            $r->setCode(43)->setSuccess(true)->setMsg("weixinPay")->setBody($body);
            $this->ajaxReturn($r->generate());
        }

        $r = new ResponseGenerator("weixinPayPickupOrder");
        $r->setCode(40)->setSuccess(false)->setMsg("Fail");
        $this->ajaxReturn($r->generate());
    }

    /**
     * 新建代取订单
     */
    public function newSendOrder()
    {
        if(notSign())
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('newSendOrder')->generate());

        if(!IS_POST)
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS("newSendOrder")->setBody(array('description'=>'无提交数据'))->generate());

        $data = I('post.');
        if(!$data)
        {
            $r = ResponseGenerator::WRONGPARAMS("newSendOrder")->setBody(array('description'=>'无提交数据'));
            $this->ajaxReturn($r->generate());
        }

        $order = new Objects\SendOrder();
        $order->setUserID(getUserID());
        $order->setSenderName($data['sender']);
        $order->setSenderPhone(($data['senderPhone']));
        $order->setSchoolName($data['school']);
        $order->setDormitoryAddress($data['dormitory']);
        $order->setRecvName($data['receiver']);
        $order->setRecvPhone($data['receiverPhone']);
        $order->setGoods($data['goods']);
        $order->setDestination($data['dest']);
        $order->setRemarks($data['remarks']);

        $orderService = new OrderService();
        $result = $orderService->newSendOrder($order);
        if($result===true)
        {
            $r = ResponseGenerator::OK('newSendOrder');
            $body = array(
                'orderNo'=>$order->getSendNo()
            );
            $r->setBody($body);
            $this->ajaxReturn($r->generate());
        }
        else
        {
            $body = array(
                'description'=>$result
            );
            $r = ResponseGenerator::WRONGPARAMS('newPickupOrder');
            $r->setBody($body);
            $this->ajaxReturn($r->generate());
        }


    }

    /**
     * 获得代取订单列表
     */
    public function getAllPickupOrders()
    {
        if(notSign())
        {
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('getAllPickupOrders')->generate());
        }

        $data = I('post.');

        $orderService = new OrderService();
        if(!isset($data['offset']))
            $data['offset']=0;

        //如果count值小于1，则默认获取10条数据
        if($data['count']<1)
            $data['count']=10;

        $datas = $orderService->getAllPickupOrders($data['offset'],$data['count']);
        if($datas!==false)
        {
            $r = ResponseGenerator::OK('getAllPickupOrders')
                ->setMsg("SucessToGetOrders")->setBody($datas);
            $this->ajaxReturn($r->generate());
        }
        else
        {
            $r = ResponseGenerator::FAIL('getAllPickupOrders')
                ->setMsg('FailToFetchData');
            $this->ajaxReturn($r->generate());
        }

    }

    /**
     * 获得代寄订单列表
     */
    public function getAllSendOrders()
    {
        if(notSign())
        {
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('getAllSendOrders')->generate());
        }

        $data = I('post.');

        $orderService = new OrderService();
        if(!isset($data['offset']))
            $data['offset']=0;

        //如果count值小于1，则默认获取10条数据
        if($data['count']<1)
            $data['count']=10;

        $datas = $orderService->getAllSendOrders($data['offset'],$data['count']);
        if($datas!==false)
        {
            $r = ResponseGenerator::OK('getAllSendOrders')
                ->setMsg("SucessToGetOrders")->setBody($datas);
            $this->ajaxReturn($r->generate());
        }
        else
        {
            $r = ResponseGenerator::FAIL('getAllSendOrders')
                ->setMsg('FailToFetchData');
            $this->ajaxReturn($r->generate());
        }
    }

    /**
     * 获得代取订单详情
     */
    public function getPickupOrderDetail()
    {
        //登陆验证
        if(notSign())
        {
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('getPickupOrderDetail')->generate());
        }

        $orderNo = I('post.orderNo');
        if($orderNo=="")
        {
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS('getPickupOrderDetail')->generate());
        }

        $orderService = new OrderService();
        $data = $orderService->getPickupOrderDetail($orderNo);
        if($data===false)
        {
            $r = ResponseGenerator::FAIL('getPickupOrderDetail');
            $this->ajaxReturn($r->generate());
        }
        else
        {
            $r = ResponseGenerator::OK('getPickupOrderDetail',$data);
            $this->ajaxReturn($r->generate());
        }

    }

    /**
     * 获得代寄订单详情
     */
    public function getSendOrderDetail()
    {
        //登陆验证
        if(notSign())
        {
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('getSendOrderDetail')->generate());
        }

        $orderNo = I('post.orderNo');
        if($orderNo=="")
        {
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS('getSendOrderDetail')->generate());
        }

        $orderService = new OrderService();
        $data = $orderService->getSendOrderDetail($orderNo);
        if($data===false)
        {
            $r = ResponseGenerator::FAIL('getSendOrderDetail');
            $this->ajaxReturn($r->generate());
        }
        else
        {
            $r = ResponseGenerator::OK('getSendOrderDetail',$data);
            $this->ajaxReturn($r->generate());
        }
    }


    public function getCities()
    {
        if(notSign())
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('getCities')->generate());

        $Address = new AddressService();
        $body = $Address->getCities();
        $this->ajaxReturn(ResponseGenerator::OK('getCities',$body)->generate());
    }

    public function getSchools()
    {
        if(notSign())
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('getSchools')->generate());


        $city = I("post.city");

        if(!$city)
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS('getSchools')->generate());

        $address = new AddressService();
        $body = $address->getSchoolsAt($city);
        if($body)
        {
            $this->ajaxReturn(ResponseGenerator::OK('getSchools',$body)->generate());
        }
        else
            $this->ajaxReturn(ResponseGenerator::WRONGPARAMS('getSchools')->generate());
    }
}













