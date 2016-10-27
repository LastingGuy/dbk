<?php
namespace Home\Model;
use Think\Model;

class SendModel extends Model
{
    // protected $_map = array(

    //     'rename' => 'sender_name',
    //     'tel' => "sender_phone",
    //     'dor' => 'dormitory_id',
    //     'delivery' => 'send_goods'
    // );

    protected $_validate = array(
        array('sender_name','require','请输入发件人姓名'),
        array('sender_phone','require','请输入发件人电话'),
        array('dormitory_id','require','请填写正确的发件人地址'),
        array('sender_goods','require','请填写寄件物品'),
        array('destination','require','请填写寄件地址'),
        array('openid','require','请登录！')
    );
}
?>