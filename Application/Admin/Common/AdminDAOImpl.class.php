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
        $condition['admin_id'] = $user_id;
        if($model->where($condition)->getField('admin_passwd') == $user_passwd){
            $this->addSession($user_id);
            return  1;
        }else{
            return  0;
        }
    }

    //增加权限
    public  function  addSession($user_id){
        $model = D("admin");
        $condition['user_id'] = $user_id;
        $school = $model->where($condition)->getField('admin_school');

        //获取学校名称
        $model = D("school");
        $school_name = $model->where("school_id='$school'")->getField('school_name');
        session("admin_school_name",$school_name);
        session("admin_id",$user_id);
        session("admin_school", $school);
    }

}