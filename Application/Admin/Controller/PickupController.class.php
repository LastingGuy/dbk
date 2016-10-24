<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/22
 * Time: 22:29
 */
namespace Admin\Controller;
use Think\Controller;
use Admin\Common;
class PickupController extends Controller
{
    public function index()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

        $this->display();
    }

    //获取代收件订单
    public function  get(){
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

        $param_array['draw'] = I("get.draw");
        $param_array['start'] = I("get.start");
        $param_array['length'] = I("get.length");
        $param_array['search'] = I("get.search");

        $object = new Common\PickupDAOImpl();
        $return_data = $object->get($param_array);

        $this->ajaxReturn($return_data);
        
    }

    //导出数据
    public function export()
    {
        $object = new Common\PickupDAOImpl();
        $object->export();
    }
}