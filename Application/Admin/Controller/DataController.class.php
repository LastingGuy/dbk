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
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

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

    public function getCity(){
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

        $object = new Common\DataDAOImpl();
        $this->ajaxReturn($object->getCity());
    }

    public function getSchool(){
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

        $city = I("post.city");
        $object = new Common\DataDAOImpl();
        $this->ajaxReturn($object->getSchoolByCity($city));
    }

     public function getDataBySchool(){
         if(!session("?admin_id")) {
             header('Location:'.U("Admin/Index/index"));
         }

         $school = I("post.school");
         $object = new Common\DataDAOImpl();
         $result['all_pickup_orders'] = $object->getNrOfPickupOrders($school);
         $result['today_pickup_orders'] = $object->getTodayNrOfPickupOrders($school);
         $result['all_send_orders'] = $object->getNrOfSendOrders($school);
         $result['today_send_orders'] = $object->getTodayNrOfSendOrders($school);

         $this->ajaxReturn($result);
     }
}