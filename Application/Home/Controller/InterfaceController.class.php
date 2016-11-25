<?php
/**
* Author: Wang Jinglu
* Date:2016/11/01
* Description:
*   后台接口
*/
namespace Home\Controller;
use Think\Controller;
use Home\Common;
import("Org.WeixinPay.WxPay#Api",null,".php");

// import("Org.WeixinPay.WxPay#Notify",null,".php");
// import("Org.weixinPay.WxPay#JsApiPay",null,".php");

class InterfaceController extends Controller
{
    //默认跳转至个人中心
    public function index()
    {
        $this->redirect('..\usercenter\index');
    }

    //获得全部城市
    public function getCitys()
    {
       $this->ajaxReturn(getCitys_local());
    }

    //获得学校
    public function getSchools()
    {
        $city = I('get.city');
        $this->ajaxReturn(getSchools_local($city));
    }

    //获得寝室信息
    public function getDors()
    {
        $school = I('get.school');
        // $school = "浙江大学城市学院";
        $this->ajaxReturn(getDormitory_local($school));
    }

    //获得快递点信息
    public function getExpress()
    {
         $school = I('get.school');
        //  $school = '浙江大学城市学院';
         $this->ajaxReturn(getExpress_local($school));
    }

    //获得寝室和快递点信息
    public function getDorsAndExpress()
    {
         $school = I('get.school');
        //  $school = '浙江大学城市学院';
         $dors = getDormitory_local($school);
         $express = getExpress_local($school);
         $typesOfExpress = getExpressSize_local($school);

         $return = array
         (
             'dors'=>$dors,
             'express'=>$express,
             'typesOfExpress'=>$typesOfExpress
         );
         $this->ajaxReturn($return);

    }

    //计算价格
    public function charge()
    {
        $school = I('get.school');
        $type = I('get.type');
        // $school = '浙江大学城市学院';
        // $type = 'size2';

        $charge = getPrice($school,$type,true);
        if($charge==-100)
        {
            $this->ajaxReturn('无价格信息');
        }
        else
        {
            $str = $charge['price'].'元';
            if($charge['addition']!="")
            {
                $addition = $charge['addition'];
                $str.="($addition)";
            }
            $this->ajaxReturn($str);
        }
    }

    //微信支付接口
    public function weixinPay()
    {
        // $openid = 'oF6atwIKrnG44UaIGPsSGDZUGmmk';
        // session('weixin_user',$openid);

        //新建代寄订单
        $orderDAO = new Common\OrderDAOlmpl();
        $response = $orderDAO->newRecvOrder();

        if($response->getSuccess())               //订单新建成功，进行微信支付
        {
            $order = $response->getBody();    //获得订单信息

            if($response->getCode()==1) //订单价格不为0，进行微信支付
            {
                //申请微信支付
                $this->ajaxReturn(Common\WeixinPayUtil::recvOrder_weixinPay($response->getBody())->generate());
            }
            else
            {
                $this->ajaxReturn($response->setMsg('下单成功')->generate());
            }


            //新建订单

            // //商户订单号  日期+类型（11代表带取订单、付款）+订单号
            // $trade_no = \WxPayConfig::MCHID.date("Ymd").'11'.$orderInfo['pickup_id'];
            // //②、统一下单
            // $input = new \WxPayUnifiedOrder();
            // $input->SetBody("dbk");
            // $input->SetAttach("test");
            // $input->SetOut_trade_no($trade_no);
            // $input->SetTotal_fee("1");
            // $input->SetTime_start(date("YmdHis"));
            // $input->SetTime_expire(date("YmdHis", time() + 600));
            // $input->SetGoods_tag("test");
            // $input->SetNotify_url("http://daibuke.cn/dbktest/index.php/home/interface/weixinNotify");
            // $input->SetTrade_type("JSAPI");
            // $input->SetOpenid(session("weixin_user"));
            // $order = \WxPayApi::unifiedOrder($input);

            // if($order['return_code']!='SUCCESS')
            // {
            //     $this->ajaxReturn($result->setCode(0)->setSuccess(false)->setMsg($order['return_msg'])->setBody(array())->generate());
            // }
            // else if($order['result_code']!='SUCCESS')
            // {
            //     $this->ajaxReturn($result->setCode(0)->setSuccess(false)->setMsg($order['err_code_des'])->setBody(array())->generate());
            // }

            // $wxpayData = array
            // (
            //     'trade_no'=>$trade_no,
            //     'openid'=>session("weixin_user"),
            //     'order_id'=>$orderInfo['pickup_id'],
            //     'nonce_str'=>$order['nonce_str'],
            //     'sign'=>$order['sign'],
            //     'prepay_id'=>$order['prepay_id'],
            //     'pay_type'=>1,
            //     'pay_status'=>0,
            //     'total_fee'=>$input->getTotal_fee(),
            //     'time_start'=>$input->getTime_start(),
            //     'time_expire'=>$input->getTime_expire()
            // );

            // $wxpayModel = M('weixinPay');
            // $wxpayModel->add($wxpayData);

            // $tools = new \JsApiPay();
            // $str = $tools->GetJsApiParameters($order);
            // $this->ajaxReturn($result->setCode(0)->setBody($str)->generate());
            //返回数据
        }
        else
        {
            $this->ajaxReturn($response->generate());
        }

    }

    //微信支付通知接口
    public function weixinNotify(){
        \Think\Log::write('测试日志信息，支付通知接口开始','WARN');
        $object = new \WxPayNotify();
        $object->Handle();
    }

   
}
?>