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

class PickupOrderDAO extends Models
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

    /**查找分页
     * @param $userid
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function selectByPagination($userid, $offset, $limit){
        $data = self::M_pickup()->where("userid='$userid' and express_status<100")->order("time desc")->
        limit("$offset,$limit")->select();
        return $data;
    }

    public function orderDetail($userid,$orderNo)
    {
        $model = self::M_pickup_view();
        $data = $model->where("userid = '%s' and pickup_no = %s",$userid,$orderNo)->find();
        return $data;
    }

    public static function orderID()
    {
        $date = date('ymd');
        $stamp = time() % 100000;
        $random = rand(100,999);
        return $date.$stamp.'1'.$random;
    }

    public function changeStatus($orderNo,$status)
    {
        $model = self::M_pickup();
        return $model->where("pickup_no = '%s'",$orderNo)->setField("express_status",$status);
    }


}