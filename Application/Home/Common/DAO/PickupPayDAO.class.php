<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/1/26
 * Time: 13:32
 */

namespace Home\Common\DAO;
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
        $data['tota_fee'] = $pickup_pay->getTotalFee();
        $data['pay_fee'] = $pickup_pay->getPayFee();
        $data['coupon_id'] = $pickup_pay->getCouponId();
        $data['pay_status'] = $pickup_pay->getPayStatus();
        $data['time_start'] = $pickup_pay->getTimeStart();
        $data['time_expire'] = $pickup_pay->getTimeExpire();

        self::M_pickup_pay()->add($data);

    }


    /**更新，完成支付
     * @param PickupPay $pickup_pay
     * @return mixed
     */
    public function updatePay($pickup_pay){

        $data['time_end'] = $pickup_pay->getTimeEnd();
        $data['transaction_id'] = $pickup_pay->getTransactionId();
        $data['pay_status'] = $pickup_pay->getPayStatus();

        self::M_pickup_pay()->save($data);
    }

    /**更新，退款
     * @param PickupPay $pickup_pay
     * @return mixed
     */
    public function updateFefund($pickup_pay){
        $data['pickup_id'] = $pickup_pay->getPickupId();
        $data['refund_fee'] = $pickup_pay->getRefundFee();
        $data['refund_time'] = $pickup_pay->getRefundTime();
        $data['refund_id'] = $pickup_pay->getRefundId();

        self::M_pickup_pay()->save($data);
    }
}

