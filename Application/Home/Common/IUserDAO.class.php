<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/24
 * Time: 21:32
 */
namespace Home\Common;

interface IUserDAO{
    //用户登录验证，支持正常登录和微信登录
    public function login($user_id,$user_passwd,$openid);
}