<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/23
 * Time: 16:48
 */
namespace Admin\Common;
class SendDAOImpl implements ISendDAO{

    //获得代寄件
    public function get($param){
        $school = session("admin_school");
        $model = M('send_view');

        $return_data['draw'] = $param["draw"];
        $return_data['recordsTotal'] = $model->count();
        $return_data['recordsFiltered'] = $model->count();

        //获取订单
        $return_data['data'] = $model->where("school_id='$school'")->select();
        foreach($return_data['data'] as $key=>$value){
            $return_data['data'][$key]['look'] = "<img src='".__ROOT__."/Public/assets/advanced-datatable/examples/examples_support/details_open.png'>";
        }
        return $return_data;
    }
}