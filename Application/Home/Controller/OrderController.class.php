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

class OrderController extends Controller
{
    public  function __construct()
    {
        //测试时使用
//        session('userid','f1bfa2d2-de42-11e6-93f6-00163e12bad6');
    }

    public function index()
    {
        echo '#^_^#';

    }


    /**
     * 新建代取订单                            未完成
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
