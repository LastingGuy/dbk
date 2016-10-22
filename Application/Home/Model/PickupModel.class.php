<?php
namespace Home\Model;
use Think\Model;
class PickupModel extends Model
{
    protected $_map = array
    (

        'rename' => 'receiver_name',
        'tel' => 'receiver_phone',
        'dor' => 'dormitory_id',
        'express' => 'express_company',
        'fetch_code' => 'express_code',
    );

    protected $_validate = array
    (
        array('receiver_name','require','请输入收件人姓名'),
        array('receiver_phone','require','请输入收件人电话')
    );
}

?>