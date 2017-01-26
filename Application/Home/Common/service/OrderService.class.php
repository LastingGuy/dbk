<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 18:41
 */

namespace Home\Common\Service;


use Home\Common\DAO\pickupOrderDAO;
use Home\Common\Objects\PickupOrder;

class OrderService
{
    /**新建订单                                 未完成
     * @param PickupOrder $order
     * @return bool|string
     */
    public function newPickupOrder(PickupOrder $order)
    {

        if(!$order->checkSchoolName())
            return '不存在此学校';

        if(!$order->checkDorAddress())
            return '不存在此寝室楼';

        if(!$order->checkReceiverName())
            return '收件人不能为空';

        if(!$order->checkReceiverPhone())
            return '手机号格式错误';

        if(!$order->checkExpressCompany())
            return '请选择快递公司';

        if(!$order->checkExpressSize())
            return '请选择快递类型';

        if(!$order->checkSMS())
            return '快递短信不能为空';

        if(!$order->checkExpressCode())
            return '取件码错误';

        $dao = new pickupOrderDAO();
        if(!$dao->newOrder($order))
            return '新建订单失败，请重新尝试';

        /////////////以下为写入pay表部分代码


        //////////////////////////////////
        return true;

    }


}