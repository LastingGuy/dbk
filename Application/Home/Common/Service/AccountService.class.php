<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 25/01/2017
 * Time: 14:18
 */

namespace Home\Common\Service;


use Home\Common\DAO\UserDAO;

class AccountService
{
    /**微信登陆
     * @param $openid
     * @return bool
     */
    public function WechatLogin($openid)
    {
        $userDAO = new UserDAO();
        if($userDAO->isUserExist($openid))
        {
            return true;
        }
        else
        {
            return $userDAO->addUser($openid);
        }
    }



}