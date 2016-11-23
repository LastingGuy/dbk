<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/11/23
 * Time: 10:22
 */
namespace Home\Common;
import("Org.WeixinPay.WxPay#Api",null,".php");
class WeixinPayUtil{

    //微信支付统一下单
    static function weixinPay(){
        //插入订单记录到pickup
        $order = new OrderDAOlmpl();
        $result = $order->newRecvOrder();

        if($result->getSuccess())
        {
            $orderInfo = $result->getBody();

            //商户订单号  日期+类型（11代表带取订单、付款）+订单号
            $trade_no = \WxPayConfig::MCHID.date("Ymd").'11'.$orderInfo['pickup_id'];
            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody("dbk");
            $input->SetAttach("test");
            $input->SetOut_trade_no($trade_no);
            $input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://daibuke.cn/dbktest/index.php/home/interface/weixinNotify");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid(session("weixin_user"));
            $order = \WxPayApi::unifiedOrder($input);

            if($order['return_code']!='SUCCESS')
            {
                return $result->setCode(0)->setSuccess(false)->setMsg($order['return_msg'])->setBody(array())->generate();
            }
            else if($order['result_code']!='SUCCESS')
            {
                return $result->setCode(0)->setSuccess(false)->setMsg($order['err_code_des'])->setBody(array())->generate();
            }

            $wxpayData = array
            (
                'trade_no'=>$trade_no,
                'openid'=>session("weixin_user"),
                'order_id'=>$orderInfo['pickup_id'],
                'nonce_str'=>$order['nonce_str'],
                'sign'=>$order['sign'],
                'prepay_id'=>$order['prepay_id'],
                'pay_type'=>1,
                'pay_status'=>0,
                'total_fee'=>$input->getTotal_fee(),
                'time_start'=>$input->getTime_start(),
                'time_expire'=>$input->getTime_expire()
            );

            $wxpayModel = M('weixinPay');
            $wxpayModel->add($wxpayData);
            // $time = time();
            // $data = array
            // (
            //     'appid'=>$order['appid'],
            //     'timestamp'=>"$time",
            //     'nonce_str'=>$order['nonce_str'],
            //     'perpay_id'=>$order['prepay_id'],
            //     'sign'=>$order['sign']
            // );


            $tools = new \JsApiPay();
            $str = $tools->GetJsApiParameters($order);
            return $result->setCode(0)->setBody($str)->generate();
            //返回数据
        }
        else
        {
            return $result->generate();
        }
    }

    //微信支付通知
    static function weixinNotify(){
        \Think\Log::write('测试日志信息，支付通知接口开始','WARN');
        $object = new \WxPayNotify();
        $object->Handle();
    }

    //微信支付退款
    static function weixinRefund(){
        
    }
    
    //微信支付退款查询
    static function weixinRefundQuery($order_id){
        $model = M("pickup");
        $openid = session("weixin_user");

        //先查看该订单是否有并且属于这个用户，然后执行查询退款
        if($model->where("pickup_id = $order_id and openid= $openid")->find()){
            $model = M("weixin_pay");
            $pickup = $model->where("order_id=$order_id and pay_type=1")->find();
            $transaction_id = $pickup['transaction_id'];
            $input = new \WxPayOrderQuery();
            $input->SetTransaction_id($transaction_id);
            $result = \WxPayApi::orderQuery($input);
            //$result中的return_code是SUCCESS时并且result_code为SUCCESS时，退款成功。
        }

    }
}