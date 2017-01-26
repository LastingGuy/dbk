<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/1/24
 * Time: 12:29
 */
namespace Admin\Controller;
use Think\Controller;
use Admin\Common;
class SchoolController extends Controller
{
    public function index(){
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

        $this->display();
    }

    public function serviceSwitch()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }
        
        $service = I("get.service");
        $status = I("get.status");

        $object = new Common\DAO\SchoolDAO();
        $object->updateOnline(session("admin_school"),$service,$status);
    }

    public function getSchool(){
        $object = new Common\DAO\SchoolDAO();
        $school = $object->getSchool(session("admin_school"));
        $this->ajaxReturn($school);
    }
}