<?php
namespace Home\Model;
use Think\Model\ViewModel;

class DormitoryViewModel extends ViewModel
{
    public $viewFields = array(
        'dormitory' => array('dormitory_id','school_id','dormitory_address'),
        'school'=>array('school_id','school_name','school_city','_on'=>'dormitory.school_id=school.school_id')
    );
}
?>