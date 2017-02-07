<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/1/26
 * Time: 13:32
 */

namespace Home\Common\DAO;
use Admin\Model\PickupModel;
use Home\Common\Objects\PickupPay;
class PickupPayDAO extends Models
{

    /**插入订单基本信息,
     * @param PickupPay $pickup_pay
     * @return mixed
     */
    public function insertPickupWithPay($pickup_pay)
    {
        $data['pickup_id'] = $pickup_pay->getPickupId();
        $data['total_fee'] = $pickup_pay->getTotalFee();
        $data['pay_fee'] = $pickup_pay->getPayFee();
        $data['coupon_id'] = $pickup_pay->getCouponId();
        $data['pay_status'] = $pickup_pay->getPayStatus();
        $data['time_start'] = $pickup_pay->getTimeStart();
        $data['time_expire'] = $pickup_pay->getTimeExpire();

        self::M_pickup_pay()->add($data);
    }


    /**更新
     * @param PickupPay $pickup_pay
     * @return mixed
     */
    public function update($pickup_pay){
        $data['pickup_id'] = $pickup_pay->getPickupId();
        $data['total_fee'] = $pickup_pay->getTotalFee();
        $data['pay_fee'] = $pickup_pay->getPayFee();
        $data['coupon_id'] = $pickup_pay->getCouponId();
        $data['pay_status'] = $pickup_pay->getPayStatus();
        $data['time_start'] = $pickup_pay->getTimeStart();
        $data['time_end'] = $pickup_pay->getTimeEnd();
        $data['time_expire'] = $pickup_pay->getTimeExpire();
        $data['transaction_id'] = $pickup_pay->getTransactionId();
        $data['refund_id'] = $pickup_pay->getRefundId();
        $data['refund_fee'] = $pickup_pay->getRefundFee();
        $data['refund_time'] = $pickup_pay->getRefundTime();
        return self::M_pickup_pay()->save($data);
    }

    /**通过pickup_no查询PickupPay
     * @param  $pickup_no
     * @return PickupPay|false
     */
    public function findPickupPay($pickup_no)
    {
        $pickupModel = self::M_pickup();
        if($data = $pickupModel->where("pickup_no=$pickup_no")->find())
        {
            $pickupPayModel = self::M_pickup_pay();
            $data = $pickupPayModel->where("pickup_id=".$data['pickup_id'])->find();
            $object = new PickupPay();
            $object->setCouponId($data['coupon_id'])->setPayFee($data['pay_fee'])->setPayStatus($data['pay_status'])
                ->setPickupId($data['pickup_id'])->setRefundId($data['refund_id'])->setRefundTime($data['refund_time'])
                ->setRefundFee($data['refund_fee'])->setTimeStart($data['time_start'])->setTimeEnd($data['time_end'])
                ->setTimeExpire($data['time_expire'])->setTotalFee($data['total_fee'])->setTransactionId($data['transaction_id']);
            return $object;
        }
        else{
            return false;
        }

    }

}

