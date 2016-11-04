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

    public function getSchools()
    {
        $city = I('get.city');
        $this->ajaxReturn(getSchools_local($city));
    }

    public function getDors()
    {
        $school = I('get.school');
        // $school = "浙江大学城市学院";
        $this->ajaxReturn(getDormitory_local($school));
    }
    public function getExpress()
    {
         $school = I('get.school');
        //  $school = '浙江大学城市学院';
         $this->ajaxReturn(getExpress_local($school));
    }

    public function getDorsAndExpress()
    {
         $school = I('get.school');
        //  $school = '浙江大学城市学院';
         $dors = getDormitory_local($school);
         $express = getExpress_local($school);

         $return = array
         (
             'dors'=>$dors,
             'express'=>$express
         );
         $this->ajaxReturn($return);

    }


}
?>