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