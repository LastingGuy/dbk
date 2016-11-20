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

         $return = array
         (
             'dors'=>$dors,
             'express'=>$express
         );
         $this->ajaxReturn($return);

    }

    //计算价格
    public function charge()
    {
        $school = I('get.school');
        $type = I('get.type');

        $charge = getPrice($school,$type);
        if($charge==-100)
        {
            $this->ajaxReturn('无价格信息');
        }
        else
        {
            if($charge==-1)
            {
                $this->ajaxReturn('底价2元，每增加1千克增加1元');
            }
            else
            {
                $this->ajaxReturn($charge.'元');
            }
        }
    }

    //微信支付接口
    public function weixinPay()
    {
        $openid = 'oF6atwNyAc4wlpgNVWTdQi4kj7Po';
        session('weixin_user',$openid);

        //插入订单记录到pickup


        //插入微信支付流水号


        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid(session("weixin_user"));
        $order = \WxPayApi::unifiedOrder($input);
        var_dump($order);

        //返回数据
    }

    //微信支付通知接口
    public function weixinNotify(){
        $object = new \WxPayNotify();
        $object->Handle();
    }
}
?>