<?php
/**
 * Created by PhpStorm.
 * User: nelson
 * Date: 2017/1/28
 * Time: 上午10:34
 */

namespace Home\Controller;
use Think\Controller;
use Home\Common;

class InvitationController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // test
        //$openid = 'oF6atwIKrnG44UaIGPsSGDZUGmma';
        //session('weixin_user',$openid);

    }

    public function index()
    {
        echo '#^_^# invitation test';
    }

    //获取邀请码，没有邀请码随机生成后返回
    public function getCode(){
        $object = new Common\InvitationDAOImpl();
        $this->ajaxReturn($object->getCode());
    }

    //验证邀请码
    public function checkCode(){
        $code = I('get.code');
        $object = new Common\InvitationDAOImpl();
        $this->ajaxReturn($object->checkInvitation($code));
    }

    //验证优惠券,返回可抵消的金额
    public function checkCoupon(){
        $object = new Common\InvitationDAOImpl();
        $this->ajaxReturn($object->checkCoupon());
    }
}