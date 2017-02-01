<?php
/**
 * Created by PhpStorm.
 * User: nelson
 * Date: 2017/1/29
 * Time: 上午10:54
 */

namespace Home\Common;

interface IInvitationDAO
{
    public function getCode();
    public function checkInvitation($code);
    public function checkCoupon();
    public function enableCoupon($couponCode);
    public function useCoupon($couponCode);
}
?>