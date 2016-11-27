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
    static function recvOrder_weixinPay($orderInfo)
    {

        $response = new ResponseGenerator("weixinpay");

        G('begin');
        //商户订单号  日期+类型（11代表带取订单、付款）+订单号
        $trade_no = \WxPayConfig::MCHID.date("Ymd").'11'.$orderInfo['pickup_id'];
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("代步客");
        $input->SetAttach("代步客");
        $input->SetOut_trade_no($trade_no);
        $input->SetTotal_fee($orderInfo['price']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://daibuke.cn/dbk/index.php/home/interface/weixinNotify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid(session("weixin_user"));
        $order = \WxPayApi::unifiedOrder($input);
        G('end');
        \Think\Log::write(G('begin','end').'s','WARN');

        //echo G('begin','end').'s';
        if($order['return_code']!='SUCCESS')
        {
            return $response->setCode(0)->setSuccess(false)->setMsg($order['return_msg']);
        }
        else if($order['result_code']!='SUCCESS')
        {
            return $response->setCode(0)->setSuccess(false)->setMsg($order['err_code_des']);
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
            'total_fee'=>$input->getTotal_fee()/100,
            'time_start'=>$input->getTime_start(),
            'time_expire'=>$input->getTime_expire()
        );

        $wxpayModel = M('weixinPay');
        $wxpayModel->add($wxpayData);

        $tools = new \JsApiPay();
        $str = $tools->GetJsApiParameters($order);
        return $response->setCode(1)->setSuccess(true)->setBody($str);


        // //插入订单记录到pickup
        // $order = new OrderDAOlmpl();
        // $result = $order->newRecvOrder();

        // if($result->getSuccess())
        // {
        //     $orderInfo = $result->getBody();

        //     //商户订单号  日期+类型（11代表带取订单、付款）+订单号
        //     $trade_no = \WxPayConfig::MCHID.date("Ymd").'11'.$orderInfo['pickup_id'];
        //     //②、统一下单
        //     $input = new \WxPayUnifiedOrder();
        //     $input->SetBody("dbk");
        //     $input->SetAttach("test");
        //     $input->SetOut_trade_no($trade_no);
        //     $input->SetTotal_fee("1");
        //     $input->SetTime_start(date("YmdHis"));
        //     $input->SetTime_expire(date("YmdHis", time() + 600));
        //     $input->SetGoods_tag("test");
        //     $input->SetNotify_url("http://daibuke.cn/dbktest/index.php/home/interface/weixinNotify");
        //     $input->SetTrade_type("JSAPI");
        //     $input->SetOpenid(session("weixin_user"));
        //     $order = \WxPayApi::unifiedOrder($input);

        //     if($order['return_code']!='SUCCESS')
        //     {
        //         return $result->setCode(0)->setSuccess(false)->setMsg($order['return_msg'])->setBody(array())->generate();
        //     }
        //     else if($order['result_code']!='SUCCESS')
        //     {
        //         return $result->setCode(0)->setSuccess(false)->setMsg($order['err_code_des'])->setBody(array())->generate();
        //     }

        //     $wxpayData = array
        //     (
        //         'trade_no'=>$trade_no,
        //         'openid'=>session("weixin_user"),
        //         'order_id'=>$orderInfo['pickup_id'],
        //         'nonce_str'=>$order['nonce_str'],
        //         'sign'=>$order['sign'],
        //         'prepay_id'=>$order['prepay_id'],
        //         'pay_type'=>1,
        //         'pay_status'=>0,
        //         'total_fee'=>$input->getTotal_fee(),
        //         'time_start'=>$input->getTime_start(),
        //         'time_expire'=>$input->getTime_expire()
        //     );

        //     $wxpayModel = M('weixinPay');
        //     $wxpayModel->add($wxpayData);

        //     $tools = new \JsApiPay();
        //     $str = $tools->GetJsApiParameters($order);
        //     return $result->setCode(0)->setBody($str)->generate();
        //     //返回数据
        // }
        // else
        // {
        //     return $result->generate();
        // }
    }

    //微信支付通知
    static function weixinNotify(){
        \Think\Log::write('测试日志信息，支付通知接口开始','WARN');
        $object = new \WxPayNotify();
        $object->Handle();
    }

    //微信支付退款
    static function refundRecvOrder($id)
    {
        $response = new ResponseGenerator("refund");
        $mod = M("weixin_pay");
        $pay = $mod->where("order_id=$id and pay_type=1")->find();
        if($pay!=false&&$pay!=null) 
        {
            \Think\Log::write('开始申请退款','INFO');
            //商户退款单号 日期+类型（10代表代取订单、退款）+订单号
            $out_refund_no = \WxPayConfig::MCHID.date("Ymd").'10'.$id;
            //向微信申请退款
            $input = new \WxPayRefund();
            $input->SetOut_trade_no($pay['trade_no']);
            $input->SetTotal_fee($pay['total_fee']*100);
            $input->SetRefund_fee($pay['total_fee']*100);
            $input->SetOut_refund_no($out_refund_no);
            $input->SetOp_user_id(\WxPayConfig::MCHID);
            $result = \WxPayApi::refund($input);
            \Think\Log::write("return_msg:".$result['return_msg'],'INFO');
            //退款成功
            if($result['return_code']=="SUCCESS"&&$result['result_code']=='SUCCESS')
            {
                \Think\Log::write('申请退款成功','INFO');

                //查找是否有退款记录，如果没有退款记录则插入
                $mod = M("weixin_refund");
                if (!($refund = $mod->where("trade_no='%s'" , $pay['trade_no'])->find())) 
                {
                    $refund['refund_no'] = $out_refund_no;
                    $refund['openid'] = $pay['openid'];
                    $refund['order_id']=$pay['order_id'];
                    $refund['pay_type'] = $pay['pay_type'];
                    $refund['trade_no'] = $pay['trade_no'];
                    $refund['total_fee'] = $pay['total_fee'];
                    $refund['refund_fee'] = $pay['total_fee'];
                    $refund['refund_time'] = date('Y-m-d H:i:s');
                    $refund['refund_id'] = $result['refund_id'];
                    $mod->add($refund);                  
                }
               // $model->where("pickup_id='$id'")->setField('express_status',4);
                return $response->setSuccess(true)->setCode(1)->setMsg("申请退款成功");
            }
            else
            {
                return $response->setCode(6)->setMsg("申请退款失败");
            }

        }
        else
        {
            return $response->setCode(6)->setMsg("申请退款失败");
        }
    }
    
    //微信支付退款查询
    static function weixinRefundQuery($order_id)
    {
        $pickupmodel = M("pickup");
        $openid = session("weixin_user");
         \Think\Log::write('查询订单','WARN');

        //先查看该订单是否有并且属于这个用户，然后执行查询退款
        $order = $pickupmodel->where("pickup_id = $order_id and openid= '$openid'")->find();
        if($order!=false&&$order!=null)
        {
             \Think\Log::write('找到订单','WARN');
            if($order['express_status']==4) //对退款中订单进行退款查询操作
            {   
                \Think\Log::write('开始修改','WARN');
                $model = M("weixin_pay");
                $pickup = $model->where("order_id=$order_id and pay_type=1")->find();
                if($pickup!=null&&$pickup!=false)
                {   
                    $transaction_id = $pickup['transaction_id'];
                    $input = new \WxPayRefundQuery();
                    $input->SetTransaction_id($transaction_id);
                    $result = \WxPayApi::refundQuery($input);
                    \Think\Log::write($result['return_code'],'WARN');
                    \Think\Log::write($result['return_msg'],'WARN');
                    \Think\Log::write($result['result_code'],'WARN');
                    \Think\Log::write($result['err_code'],'WARN');
                    \Think\Log::write($result['refund_status_0'],'WARN');
                    //$result中的return_code是SUCCESS时并且result_code为SUCCESS时，退款成功。
                    if($result['return_code']=='SUCCESS'&&$result['result_code']=='SUCCESS'&&$result['refund_status_0']=='SUCCESS')
                    {
                        $pickupmodel->where("pickup_id='%s'",$order_id)->setField('express_status',5);

                    }
                }
            }
        }

    }
}