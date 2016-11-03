<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/11/3
 * Time: 20:25
 */
namespace Admin\Controller;
use Think\Controller;
use Admin\Common;
class DataController extends Controller {

    public function index(){

        //准备权限
        $this->assign("admin_school",session("admin_school"));
        $this->assign("admin_type",session("admin_type"));

        //设置订单数
        $object = new Common\DataDAOImpl();
        $all_pickup_orders = $object->getNrOfPickupOrders();
        $today_pickup_orders = $object->getTodayNrOfPickupOrders();
        $all_send_orders = $object->getNrOfSendOrders();
        $today_send_orders = $object->getTodayNrOfSendOrders();

        $this->assign("all_pickup_orders",$all_pickup_orders);
        $this->assign("today_pickup_orders",$today_pickup_orders);
        $this->assign("all_send_orders",$all_send_orders);
        $this->assign("today_send_orders",$today_send_orders);

        $this->display();
    }

}