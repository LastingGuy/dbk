<?php
namespace Home\Model;
use Think\Model;
class PickupModel extends Model
{
    protected $_map = array(

        'rename' => 'receiver_name',
        'tel' => 'receiver_phone',
        'dor' => 'dormitory_id',
        'express' => 'express_company',
        'fetch_code' => 'express_code',
    );

    protected $_validate = array(
        array('receiver_name','require','请输入收件人姓名'),
        array('receiver_phone','require','请输入收件人电话'),
        array('express_company','请选择','请选择快递公司',0,'notequal'),
        array('express_type','请选择','请选择快递类型',0,'notequal'),
        array('dormitory_id','require','请填写正确的收货人地址'),
        array('express_sms','require','请输入快递公司短信'),
        array('express_code','require','请输入提取码'),
        array('price','require','请输入价格')
    );
}

?>