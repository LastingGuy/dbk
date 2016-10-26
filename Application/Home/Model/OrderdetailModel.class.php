<?php
namespace Home\Model;
use Think\Model;
class OrderdetailModel extends Model
{
    protected $tableName="pickup_view";
    protected $_map = array(
        'id'=>'pickup_id',
        'code'=>'express_code',
        'express'=>'express_company',
        'type'=>'express_type',
        'sms'=>'express_sms',
        'status'=>'express_status',
        'city'=>'school_city',
        'school'=>'school_name',
        'dor'=>'dormitory_address',
        'READ_DATA_MAP'=>true
    );
}

?>