<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/23
 * Time: 0:02
 */
namespace Admin\Common;
interface IAdminDAO{
    //登录验证
    public function login($user_id, $user_passwd);

    //增加权限
    public  function  addSession($user_id);
}