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
       /* if(!session("?admin_id")) {
            header('Location'.U("Admin/Index/index"));
        }*/

        $school_id = session("admin_school");
        
        $param_array['draw'] = I("get.draw");
        $param_array['start'] = I("get.start");
        $param_array['length'] = I("get.length");
        $param_array['search'] = I("get.search");
        
        //获取学校名称
        $model = D("school");
        $school_name = $model->where("admin_school=$school_id")->getField('school_name');
        session("admin_school_name",$school_name);

        //获取订单
        $model = D('PickupView');
        $data = $model->where("admin_school=$school_id")->select();

        $this->dispaly($data);
        
    }
}