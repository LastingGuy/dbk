<?php

/**
 * Created by PhpStorm.
 * User: ben
 * Date: 06/02/2017
 * Time: 16:45
 */
namespace Home\Common\Util;
use Home\Common\DAO\PickupOrderDAO;
use Home\Common\DAO\PickupPayDAO;

import("Org.WeixinPay.WxPay#Api", null, ".php");
class PickupPayNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            \Think\Log::write('测试日志信息，输入参数不正确','WARN');
            echo $msg;
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            \Think\Log::write('测试日志信息，订单查询失败','WARN');
            echo $msg;
            return false;
        }

        $orderNo = $data['out_trade_no'];
        $paydao = new PickupPayDAO();
        $order = $paydao->findPickupPay($orderNo);
        $order->setTransactionId($data["transaction_id"]);
        $order->setPayStatus(1);
        $order->setTimeEnd(date('Y-m-d H:i:s'));
        $paydao->update($order);

        $pickupDAO = new PickupOrderDAO();
        $pickupDAO->changeStatus($orderNo,2);

    }

}