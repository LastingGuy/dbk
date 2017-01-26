<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 18:45
 */

namespace Home\Common\DAO;


use Home\Common\Objects\PickupOrder;
use Think\Exception;

class pickupOrderDAO
{
    public function newOrder(PickupOrder $order)
    {

        $flag = true;
        $count = 0;
        while ($flag)
        {
            $data['pickup_no'] = self::orderID();
            $data['userid'] = $order->getUserId();
            $data['receiver_name'] = $order->getReceiverName();
            $data['receiver_phone'] = $order->getReceiverPhone();
            $data['dormitory_id'] = $order->getDormitoryId();
            $data['express_type'] = $order->getExpressType();
            $data['express_company'] = $order->getExpressCompany();
            $data['express_sms'] = $order->getExpressSms();
            $data['express_code'] = $order->getExpressCode();
            $data['remarks'] = $order->getRemarks();
            $data['time'] = date("Y-m-d H:i:s");
            $data['express_status'] = 1;

            $model = Models::M_pickup();
            try
            {
                $order_id = $model->data($data)->add();
                $order->setPickupId($order_id);
                $order->setPickupNo($data['pickup_no']);
                $order->setOrderTime($data['time']);
                $order->setStatus($data['express_status']);
                $flag = false;
                return true;
            }
            catch(Exception $e)
            {
                $count++;
                if($count>10)
                    return false;
            }
         }
    }


    public static function orderID()
    {
        $date = date('ymd');
        $stamp = time() % 100000;
        $random = rand(1000,9999);
        return $date.$stamp.$random;
    }

}