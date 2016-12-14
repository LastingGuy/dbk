<?php
namespace Home\Model;
use Think\Model\ViewModel;

class ExpresspriceViewModel extends ViewModel
{
    public $viewFields = array
    (
        'fee' => array('_table'=>'dbk_school_fee','school_id','size','description','price','addition'),
        'school'=>array('school_id','school_name','school_city','_on'=>'fee.school_id=school.school_id')
    );
}
?>