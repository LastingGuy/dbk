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

import("Org.WeixinPay.WxPay#Api", null, ".php");


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
    public function getSchoolInfo()
    {
        $school = I('get.school');
        $dors = getDormitory_local($school);
        $express = getExpress_local($school);
        $typesOfExpress = getExpressSize_local($school);

        $return = array
        (
            'dors' => $dors,
            'express' => $express,
            'typesOfExpress' => $typesOfExpress
        );
        $response = new Common\ResponseGenerator('getSchoolInfo',true,1,"获得学校信息成功",$return);
        $r=isSchoolOnline($school);
        if($r!==true)
        {
            $response->setCode(31)->setMsg($r);
        }
        $this->ajaxReturn($response->generate());

    }

    //计算价格
    public function charge()
    {
        $school = I('get.school');
        $type = I('get.type');
        // $school = '浙江大学城市学院';
        // $type = 'size2';

        $charge = getPrice($school, $type, true);
        if ($charge == -100) {
            $this->ajaxReturn('无价格信息');
        } else {
            $str = $charge['price'] . '元';
            if ($charge['addition'] != "") {
                $addition = $charge['addition'];
                $str .= "($addition)";
            }
            $this->ajaxReturn($str);
        }
    }

    //微信支付接口
    public function weixinPay()
    {
        if (!session("?weixin_user")) {
            $response = new Common\ResponseGenerator("weixinPay", false, 0, "未登录");
            return $response->generate();
        }


        //新建代寄订单
        $orderDAO = new Common\OrderDAOlmpl();
        $response = $orderDAO->newRecvOrder();


        if ($response->getSuccess())               //订单新建成功，进行微信支付
        {
            $order = $response->getBody();    //获得订单信息

            if ($response->getCode() == 1) //订单价格不为0，进行微信支付
            {
                //申请微信支付
                $this->ajaxReturn(Common\WeixinPayUtil::recvOrder_weixinPay($response->getBody())->generate());
            } else {
                $this->ajaxReturn($response->setMsg('下单成功')->generate());
            }
        } else {
            $this->ajaxReturn($response->generate());
        }

    }

    //微信支付通知接口
    public function weixinNotify()
    {
        \Think\Log::write('测试日志信息，支付通知接口开始', 'WARN');
        $object = new \WxPayNotify();
        $object->Handle();
    }


    //获得未支付订单详情
    public function getOrderWeixinPayInfo()
    {
        $response = new Common\ResponseGenerator("getRefundOrderInfo");
        if (!session("?weixin_user"))        //未登录
        {
            $response->setCode(0)->setMsg("未登录");
            $this->ajaxReturn($response->generate());
        } else if (!IS_POST)                   //未提交参数
        {
            $response->setCode(0)->setMsg("参数错误");
            return $response->generate();
        } else {
            $orderID = I("post.orderID");
            $userID = session("weixin_user");


            $orderDAO = new Common\OrderDAOlmpl();
            $result = $orderDAO->getUnPaidOrderInfo($orderID, 1, $userID);


            if ($result->getSuccess()) {
                $response->setSuccess(true)->setMsg("ok")->setCode(1)->setBody($result->getBody());
                $this->ajaxReturn($response->generate());
            } else {
                $result->setAction($response->getAction());
                $this->ajaxReturn($result->generate());
            }
        }
    }


    //新建代寄订单
    public function newSendOrder()
    {
        //验证是否登陆
        if (!session('?weixin_user')) {
            $this->ajaxReturn('请登陆！');
        }


        if (IS_POST) {
            $orderDAO = new Common\OrderDAOlmpl();
            $this->ajaxReturn($orderDAO->newSendOrder());
        } else {
            $this->ajaxReturn('提交失败');
        }
    }

    //取消代寄订单
    public function cancelSendOrder()
    {
        $response = new Common\ResponseGenerator("cancelSendOrder");

        if (!session("?weixin_user"))    //验证登录
        {
            $response->setCode(0)->setMsg('未登录');
            $this->ajaxReturn($response->generate());
        } else if (!IS_POST)   //提交错误
        {
            $response->setCode(0)->setMsg('请求参数错误');
            $this->ajaxReturn($response->generate());
        } else {
            $id = I('post.id');
            if ($id == '') {
                $response = new Common\ResponseGenerator('deleteOrder', false, 2, "请求参数错误");
                $this->ajaxReturn($response->generate());
            }


            $orderDAO = new Common\OrderDAOlmpl();
            $response = $orderDAO->deleteSendOrder($id);
            $this->ajaxReturn($response->generate());
        }
    }

    public function getUserDefaultInfo()
    {
        $response = new Common\ResponseGenerator("getUserDefaultInfo");
        if(!session("?weixin_user"))
        {
            $response->setSuccess(false)->setCode(13)->setMsg("用户未登陆");
            $this->ajaxReturn($response->generate());
        }
        else
        {
            $id = session('weixin_user');
            $userDAO = new Common\UserDAOImpl();
            $this->ajaxReturn($userDAO->getUserDefaultInfo($id)->generate());
        }
    }
    //微信支付批量查询接口
    public function weixinQuery(){
        $model = M("weixin_pay");
        $mod = M("pickup");
        $no = I('post.no');
        if(!isset($no))
            return ;
        $data = $model->where("trade_no='%s'",$no)->select();
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


    }
}

?>