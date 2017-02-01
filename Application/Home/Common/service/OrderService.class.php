<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 18:41
 */

namespace Home\Common\Service;
import("Org.WeixinPay.WxPay#Api",null,".php");

use Home\Common\DAO\pickupOrderDAO;
use Home\Common\DAO\PickupPayDAO;
use Home\Common\DAO\UserDAO;
use Home\Common\Objects\PickupOrder;
use Home\Common\Objects\PickupPay;

class OrderService
{
    /**新建订单
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

        $dao = new PickupOrderDAO();
        if(!$dao->newOrder($order))
            return '新建订单失败，请重新尝试';

        $pickuppay = new PickupPay();
        $pickuppay->setPickupId($order->getPickupId())
            ->setTotalFee($order->getPrice())
            ->setPayStatus(0);

        $pickuppayDAO = new PickupPayDAO();
        $pickuppayDAO->insertPickupWithPay($pickuppay);

        return true;

    }


    /**获得pickupPay                          未完成
     * @param $pickupNo
     * @return mixed
     *  log：
     *      dao层中没有实现findPickupPay()函数
     */
    public function getPickupPay($pickupNo)
    {
        $pickupPayDao = new PickupPayDAO();
        return $pickupPayDao->findPickupPay($pickupNo);
    }

    public function pickupOrder_freeOrder(PickupPay $pickupPay)
    {
        //价格为0，不申请微信支付
        if($pickupPay->getTotalFee()==0)
        {

            $pickupPay->setPayFee(0)->setTimeEnd(date("YmdHis"))->setPayStatus(1);
            /////////////////////////uf
            $pickupPayDAO = new PickupPayDAO();
            if($pickupPayDAO->update($pickupPay))
            {
                return true;
            }
            else
                return false;
            //////////////////////////
        }
        return false;
    }


    /**使用代金券支付                            未完成
     * @param $pickupPay
     * @return bool
     *  log：
     *      coupon系统未实现
     */
    public function pickupOrder_withCoupon($pickupPay)
    {
        //代金券支付
        /////////////////////////////////////////////////uf
//        $couponDAO = new CouponDAO();
//        $pickupPayDao = new PickupPayDAO();
//        //是否存在代金券
//        $haveCoupon = $couponDAO->isAvailableCouponExist($userid);
//        if($haveCoupon)
//        {
//            //获得一张可用代金券ID
//            $couponID = $couponDAO->getAvailableCoupon($userid);
//            $pickupPay->setPayFee(0)->setCouponId($couponID)->setPayStatus(1)->setTimeEnd(date("YmdHis"));
//
//            if($pickupPayDao->update($pickupPay))
//            {
//                //使用代金券
//                $couponDAO->useCoupon($couponID,$pickupPay->getPickupId());
//                return $pickupPay->getPayFee();
//
//            }
//            else
//                return false;
//
//        }
        return false;
        ////////////////////////////////////////////////
    }


    /**申请微信支付                       未完成
     * @param $pickupNo
     * @param PickupPay $pickupPay
     * @return bool
     *  log：
     *      userDAO 中为实现getOpenID()功能
     *      pickupDAO中为实现update（)功能
     */
    public function pickupOrder_WexinPay($pickupNo,PickupPay $pickupPay)
    {

        $pickuppayDAO = new PickupPayDAO();


        //获得用户openid
        $openid = getOpenID();

        $time_start = date("YmdHis");
        $time_expire = date("YmdHis", time() + 600);

        $input = new \WxPayUnifiedOrder();
        $input->SetBody("代步客");
        $input->SetAttach("代步客");
        $input->SetOut_trade_no($pickupNo);
        $input->SetTotal_fee($pickupPay->getTotalFee()*100);
        $input->SetTime_start($time_start);
        $input->SetTime_expire($time_expire);
        $input->SetGoods_tag("代步客");
        $input->SetNotify_url(\WxPayConfig::NOTIFYPATH);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);
        $order = \WxPayApi::unifiedOrder($input);

        if($order['return_code']!='SUCCESS')
        {
            return false;
        }
        else if($order['result_code']!='SUCCESS')
        {
            return false;
        }


        $pickupPay->setPayFee($pickupPay->getTotalFee())
            ->setTimeStart($time_start)
            ->setTimeExpire($time_expire);

        if($pickuppayDAO->update($pickupPay))
        {
            return true;
        }

        return false;

    }


    public function finishWexinPay()
    {

    }


}