<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/22
 * Time: 22:29
 */
namespace Admin\Controller;
use Think\Controller;
class PickupController extends Controller
{
    public function index()
    {
        if(!session("?admin_id")) {
            header('Location'.U("Admin/Index/index"));
        }
        $this->display();
    }

    //获取代收件订单
    public function  get(){
        if(!session("?admin_id")) {
            header('Location'.U("Admin/Index/index"));
        }
    }
}