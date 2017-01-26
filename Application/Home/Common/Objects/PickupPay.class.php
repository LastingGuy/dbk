<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/1/26
 * Time: 13:41
 */
namespace Home\Common\Objects;

class PickupPay{

    private $pickup_id;
    private $total_fee;
    private $pay_fee;
    private $coupon_id;
    private $pay_status = 0;
    private $time_start;
    private $time_end;
    private $time_expire;
    private $transaction_id;
    private $refund_time;
    private $refund_fee;
    private $refund_id;

    /**
     * @return mixed
     */
    public function getRefundFee()
    {
        return $this->refund_fee;
    }

    /**
     * @param mixed $refund_fee
     * @return  $this
     */
    public function setRefundFee($refund_fee)
    {
        $this->refund_fee = $refund_fee;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPickupId()
    {
        return $this->pickup_id;
    }

    /**
     * @param mixed $pickup_id
     * @return  $this
     */
    public function setPickupId($pickup_id)
    {
        $this->pickup_id = $pickup_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalFee()
    {
        return $this->total_fee;
    }

    /**
     * @param mixed $total_fee
     * @return  $this
     */
    public function setTotalFee($total_fee)
    {
        $this->total_fee = $total_fee;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayFee()
    {
        return $this->pay_fee;
    }

    /**
     * @param mixed $pay_fee
     * @return  $this
     */
    public function setPayFee($pay_fee)
    {
        $this->pay_fee = $pay_fee;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCouponId()
    {
        return $this->coupon_id;
    }

    /**
     * @param mixed $coupon_id
     * @return  $this
     */
    public function setCouponId($coupon_id)
    {
        $this->coupon_id = $coupon_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPayStatus()
    {
        return $this->pay_status;
    }

    /**
     * @param int $pay_status
     * @return  $this
     */
    public function setPayStatus($pay_status)
    {
        $this->pay_status = $pay_status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeStart()
    {
        return $this->time_start;
    }

    /**
     * @param mixed
     * @return  $this
     */
    public function setTimeStart($time_start)
    {
        $this->time_start = $time_start;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeEnd()
    {
        return $this->time_end;
    }

    /**
     * @param mixed $time_end
     * @return  $this
     */
    public function setTimeEnd($time_end)
    {
        $this->time_end = $time_end;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeExpire()
    {
        return $this->time_expire;
    }

    /**
     * @param mixed $time_expire
     * @return  $this
     */
    public function setTimeExpire($time_expire)
    {
        $this->time_expire = $time_expire;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * @param mixed $transaction_id
     * @return  $this
     */
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefundTime()
    {
        return $this->refund_time;
    }

    /**
     * @param mixed $refund_time
     * @return  $this
     */
    public function setRefundTime($refund_time)
    {
        $this->refund_time = $refund_time;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefundId()
    {
        return $this->refund_id;
    }

    /**
     * @param mixed $refund_id
     * @return  $this
     */
    public function setRefundId($refund_id)
    {
        $this->refund_id = $refund_id;
        return $this;
    }
}
