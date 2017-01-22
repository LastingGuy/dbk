<?php
/**
 * Created by PhpStorm.
 * Author: Ben
 * Date: 2017/1/22
 * Time: 18:05
 * Description:
 *  订单接口
 */
namespace  Home\Controller;
use Home\Common\ResponseGenerator;
use Think\Controller;

class OrderController extends Controller
{
    public function index()
    {
        echo '#^_^#';
    }

    public function newPickupOrder()
    {
        //验证是否登录
        if(notSign())
            $this->ajaxReturn(ResponseGenerator::NOTSIGN('newPickupOrder')->generate());




    }
}
