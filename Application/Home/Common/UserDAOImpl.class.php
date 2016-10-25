<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/24
 * Time: 21:35
 */
namespace Home\Common;

class UserDAOImpl implements IUserDAO{

    //用户登录验证
    public function login($openid){

        $model = D("weixin_user");
        if($model->where("$openid = $openid")->find()){
            session("weixin_user",$openid);
        }
        else{
            $data['openid'] = $openid;
            if($model->data($data)->add()){
                session("weixin_user",$openid);
            }
        }
    }

    public function weixinLogin(){

    }
}