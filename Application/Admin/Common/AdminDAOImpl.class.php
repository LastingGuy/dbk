<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/23
 * Time: 0:08
 */
namespace Admin\Common;
class AdminDAOImpl implements IAdminDAO{
    //登录验证
    public function login($user_id, $user_passwd){
        $model = M("admin");
        var_dump($user_id);
        $data = array();
        $condition['admin_id'] = $user_id;
        if($model->where($condition)->getField('admin_passwd') == $user_passwd){
            $this->addSession($user_id);
            $data['login']  = 0;
        }else{
            $data['login']  = 1;
        }
        return $data;
    }

    //增加权限
    public  function  addSession($user_id){
        $model = D("admin");
        $condition['user_id'] = $user_id;
        $school = $model->where($condition)->getField('admin_school');
        session("admin_id",$user_id);
        session("admin_school", $school);
    }

}