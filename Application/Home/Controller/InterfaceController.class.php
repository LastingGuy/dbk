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
    //ajax返回 <option value=id>城市名</option> 格式
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
        
        $this->ajaxReturn(getDormitory_local($school));
    }



}
?>