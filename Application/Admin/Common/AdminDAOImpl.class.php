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
        $condition['admin_passwd'] = $user_passwd;

        if($model->where($condition)->find()){
            $return = $this->addSession($user_id);
            FlowRecord::Login("登录成功");
            return $return;
        }else{
            FlowRecord::Login("登录失败");
            return  0;
        }
    }

    //增加权限
    public  function  addSession($user_id){
        $model = D("admin");
        $condition['admin_id'] = $user_id;
        //查看账户类型
        $admin = $model->where($condition)->getField('admin_id,admin_school,admin_type');

        //获取学校名称
        $model = D("school");
        $school_name = $model->where("school_id='".$admin[$user_id]['admin_school']."'")->getField('school_name');
        //session增加学校id
        session("admin_school", $admin[$user_id]['admin_school']);
        //session增加学校名字
        session("admin_school_name",$school_name);
        //session增加管理员id
        session("admin_id",$user_id);
        //session增加管理员类型
        session("admin_type",$admin[$user_id]['admin_type']);

        if($admin[$user_id]['admin_type']==0)
            return 1;
        else if($admin[$user_id]['admin_type']==1)
            return 2;
    }

}