<?php

/*
*   Author:Wang Jinglu
*   Date:2016/10/31
*/
namespace Home\Common;

class OrderDAOlmpl implements IOrderDAO
{
    private $openid;
    private $sendModel;
    private $pickupModel;
    private $orderDetail;
    private $sendOrder;
    const judgeTime = 16;

    public function __construct()
    {
        $this->openid = session('weixin_user');
        $this->sendModel = M('send');
        $this->pickupModel = M('pickup');
        $this->orderDetail = D('orderdetail');
        $this->sendOrder = D('sendView');

    }
    
    public function setOpenID($id='')
    {
        if($id!='')
        {
            $this->openid = $id;
        }
        else
        {
            $this->openid = session('weixin_user');
        }
    }

    //删除代取订单
    public function deletePickupOrder($id)
    {
        $model = $this->pickupModel;
        $today = getdate();
        $stamp = mktime(self::judgeTime,0,0,$today['mon'],$today['mday'],$today['year']);

        if($today['hourse']<self::judgeTime)
        {
            $stamp -= 24 * 60 * 60;
        }
        $deadLine = date('Y-m-d H:i:s',$stamp);

        $unfinished = $model->where("openid='$this->openid' and pickup_id='$id' and express_status=2  and time>'$deadLine'")->find();
        $finished = $model->where("openid='$this->openid' and pickup_id='$id' and express_status=3")->find();
        if($finished == false && $unfinished==false)
        {
            return 5;
        }

        if($model->where("pickup_id='$id'")->setInc('express_status',100)==true)
        {
            return 1;
        }     
        else
        {
           return 6;
        }
    }

    //删除待寄订单
    public function deleteSendOrder($id)
    {
        $model = $this->sendModel;
        $today = getdate();
        $stamp = mktime(self::judgeTime,0,0,$today['mon'],$today['mday'],$today['year']);

        if($today['hourse']<self::judgeTime)
        {
            $stamp -= 24 * 60 * 60;
        }
        $deadLine = date('Y-m-d H:i:s',$stamp);

        $unfinished = $model->where("send_id='$this->openid' and pickup_id='$id' and sender_status=2  and time>'$deadLine'")->find();
        $finished = $model->where("send_id='$this->openid' and pickup_id='$id' and sender_status=3")->find();
        if($finished == false && $unfinished==false)
        {
            return 5;
        }

        if($model->where("pickup_id='$id'")->setInc('sender_status',100)==true)
        {
            return 1;
        }     
        else
        {
           return 6;
        }
    }

    //获得所有未删除代取订单
    public function getAllPickupOrders()
    {
        $datas = $this->pickupModel->where("openid='$this->openid' and express_status<100")->order('time desc')->select();
        return $datas;
    }

    //获得未完成订单
    public function getUnfinishedPickupOrders()
    {
        $datas = $this->pickupModel->where("openid='$this->openid' and express_status=2")->order('pickup_id desc')->select();
        return $datas;
    }

    //获得已完成订单
    public function getFinishedPickupOrders()
    {
        $datas = $this->pickupModel->where("openid='$this->openid' and express_status=3")->order('pickup_id desc')->select();
        return $datas;
    }
    public function getPickupOrdersByStatus($status)
    {
        switch($status)
        {
            case 0:
                return $this->getAllPickupOrders();
                break;
            case 2:
                return $this->getUnfinishedPickupOrders();
                break;
            case 3:
                return $this->getFinishedPickupOrders();
                break;
            default:
                return array();
        }
    }

    public function getAllSendOrders()
    {
        $datas = $this->sendOrder->where("openid='$this->openid' and express_status<100")->order('time desc')->select();
        return $datas;
    }
    public function getAllOrders()
    {
        return;
    }
    public function getDeleteOrders()
    {
        return;
    }

    public function getOrders($type,$status)
    {
        switch($type)
        {
            case 0: 
                return $this->getPickupOrdersByStatus($status);
                break;
            case 1:
                return $this->getPickupOrdersByStatus($status);
                break;
            default:
                return array();
        }
    }
}