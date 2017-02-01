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
use Think\Controller;
use Think\Crypt\Driver\Think;

class OrderController extends Controller
{
    public  function __construct()
    {
        //测试时使用
        session('userid','aac6112c-e2c9-11e6-93f6-00163e12bad6');
        session('openid','oF6atwIKrnG44UaIGPsSGDZUGmmk');
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

        $userid = session('userid');
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
        echo $fee;
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













