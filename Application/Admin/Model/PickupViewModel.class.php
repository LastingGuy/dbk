<?php
namespace Admin\Model;
use Think\Model\ViewModel;

class PickupViewModel extends ViewModel
{
    public $viewFields = array(
        'pickup' => array('pick_id','user_id','receiver_name','receiver_name','
        receiver_phone','dormitory_id','express_type','express_type','express_company',
        'express_sms','express_code','remarks','price','time','express_status','_on'=>'pickup.dormitory_id=dormitory.dormitory_id'),

        'dormitory' => array('dormitory_id','school_id','dormitory_address'),
        'school'=>array('school_id','school_name','school_city','_on'=>'dormitory.school_id=school.school_id')
    );
}
?>