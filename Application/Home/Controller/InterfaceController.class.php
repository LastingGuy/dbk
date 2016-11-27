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

   //微信支付批量查询接口
    /*public function weixinQuery(){
        $model = M("weixin_pay");
        $mod = M("pickup");
        $data = $model->where("time_start>'2016-11-27 12:30:00'")->select();
        foreach ($data as $key=>$value){
            $out_trade_no = $value['trade_no'];
            $input = new \WxPayOrderQuery();
            $input->SetOut_trade_no($out_trade_no);
            $result = \WxPayApi::orderQuery($input);

            if($result["return_code"] == "SUCCESS"
                && $result["result_code"] == "SUCCESS"){

                $new['trade_no'] = $out_trade_no;
                $new['time_end'] = $result['time_end'];
                $new['transaction_id'] = $result['transaction_id'];
                $new['pay_status'] = 1;
                $model->save($new);

                $pickup['pickup_id'] = $value['order_id'];
                $pickup['express_status'] = 2;
                $pickup['pay_time'] = $result['time_end'];
                $mod->save($pickup);
            }

        }


    }*/
}
?>