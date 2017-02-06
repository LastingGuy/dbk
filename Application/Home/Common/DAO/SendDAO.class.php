<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 03/02/2017
 * Time: 13:58
 */

namespace Home\Common\DAO;


use Home\Common\Objects\SendOrder;
use Think\Exception;

class SendDAO extends Models
{
    /**新建代寄订单
     * @param SendOrder $sendOrder
     * @return bool
     */
    public function addOrder(SendOrder $sendOrder)
    {
        $flag = true;
        $count = 0;
        $data=array(
            'userid' => getUserID(),
            'sender_name' => $sendOrder->getSenderName(),
            'sender_phone' =>$sendOrder->getSenderPhone(),
            'dormitory_id' =>$sendOrder->getDormitoryId(),
            'recv_name'=>$sendOrder->getRecvName(),
            'recv_phone'=>$sendOrder->getRecvPhone(),
            'sender_goods'=>$sendOrder->getGoods(),
            'destination'=>$sendOrder->getDestination(),
            'remarks'=>$sendOrder->getRemarks(),
            'time'=>$sendOrder->getTime(),
            'sender_status'=>$sendOrder->getStatus()
        );
        $model = self::M_send();
        while ($flag)
        {
            try
            {
                $data['send_no'] = self::getOrderID();
                $id = $model->data($data)->add();
                $sendOrder->setSendID($id);
                $sendOrder->setSendNo($data['send_no']);
                return true;
            }
            catch (Exception $e)
            {
                if(++$count>10)
                    return false;
            }
        }


    }

    /**查找分页
     * @param $userid
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function selectByPagination($userid, $offset, $limit){
        $data = self::M_send()->where("userid='$userid'")->order("time desc")->
        limit("$offset,$limit")->select();
        return $data;
    }

    public function orderDetail($userid,$orderNo)
    {
        $model = self::M_send_view();
        $data = $model->where("userid = '%s' and send_no = %s",$userid,$orderNo)->find();
        return $data;
    }
    private static function getOrderID()
    {
        $date = date('ymd');
        $stamp = time() % 100000;
        $random = rand(100,999);
        return $date.$stamp.'2'.$random;
    }

}