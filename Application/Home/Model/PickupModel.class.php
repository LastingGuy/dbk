<?php
namespace Home\Model;
use Think\Model;
class PickupModel extends Model
{
    // protected $_map = array
    // (
    //     'city' => 'city',
    //     'school' => 'school',
    //     'rename' => 'rename',
    //     'telphone' => 'telphone',
    //     'dbuild' => 'dbuild',
    //     'express' => 'express',
    //     'express_type' => 'express_type',
    //     'price' => 'price',
    //     'express_sms' => 'express_sms',
    //     'fetch_code' => 'fetch_code',
    //     'remarks' => 'remarks'
    // );

    protected $_validate = array
    (
        array('rename','require','请输入收件人姓名'),
        array('telphone','require','请输入tel')
    );
}

?>